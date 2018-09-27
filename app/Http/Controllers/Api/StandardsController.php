<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StandardForms\StandardFormsIndex;
use App\Http\Requests\StandardForms\StandardFormStore;
use App\Http\Requests\StandardForms\StandardFormUpdate;
use App\Http\Requests\StandardForms\StatementStore;

use App\Models\StandardForm;
use App\Models\DefaultFromData;
use App\Models\StandardStatement;
use App\Models\DefaultStatement;
use App\Models\StandardScope;
use App\Models\DefaultScope;

use App\Services\Projects\StandardStatementsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use PDF;
use DB;

class StandardsController extends ApiController
{
    /**
     * @var StandardForm
     */
    private $standardForm;

    /**
     * @var DefaultFromData
     */
    private $defaultFormData;

    /**
     * @var StandardStatement
     */
    private $standardStatement;

    /**
     * @var DefaultStatement
     */
    private $defaultStatement;

    /**
     * @var StandardScope
     */
    private $standard_scope;

    /**
     * @var DefaultScope
     */
    private $default_scope;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * @var StandardStatementsService
     */
    private $standardStatementsService;

    /**
     * StandardsController constructor.
     *
     * @param StandardForm $standardForm
     * @param DefaultFromData $defaultFromData
     * @param StandardStatement $standardStatement
     * @param DefaultStatement $defaultStatement
     * @param StandardScope $standard_scope
     * @param DefaultScope $default_scope
     * @param JWTAuth $jwtAuth
     * @param StandardStatementsService $standardStatementsService
     */
    public function __construct(
        StandardForm $standardForm,
        DefaultFromData $defaultFromData,
        StandardStatement $standardStatement,
        DefaultStatement $defaultStatement,
        StandardScope $standard_scope,
        DefaultScope $default_scope,
        JWTAuth $jwtAuth,
        StandardStatementsService $standardStatementsService
    )
    {
        $this->standardForm = $standardForm;
        $this->defaultFormData = $defaultFromData;
        $this->standardStatement = $standardStatement;
        $this->defaultStatement = $defaultStatement;
        $this->jwtAuth = $jwtAuth;
        $this->standard_scope = $standard_scope;
        $this->default_scope = $default_scope;
        $this->standardStatementsService = $standardStatementsService;
    }

    /**
     * @param StandardFormsIndex $request
     *
     * @return JsonResponse
     */
    public function index(StandardFormsIndex $request): JsonResponse
    {
        $forms = $this->standardForm->all();

        return $this->respond($forms);
    }

    /**
     * @param int $formId
     * @return JsonResponse
     */
    public function show(int $formId): JsonResponse
    {
        $form = $this->standardForm->where('form_id', $formId)->first();
        /** Workaround for insane flow */
        $statements = $this->standardStatement->where('form_id', $formId)->get();
        if ($statements->isEmpty() && auth()->user()->company) {
            $this->standardStatementsService->createDefaultStatements(auth()->user()->company);
        }
        $statements = $this->standardStatement->where('form_id', $formId)->get();

        if (!$statements->count()) {
            $statements =  $this->defaultStatement
                ->where('form_id', $formId)
                ->get()
                ->toArray();
            foreach ($statements as $key => $defaultStatement) {
                $this->standardStatement->create([
                    'form_id' => $defaultStatement['form_id'],
                    'company_id' => auth()->user()->company_id,
                    'title' => $defaultStatement['title'],
                    'statement' => $defaultStatement['statement'],
                ]);
            }
        }

        return $this->respond([
            'form' => $form,
            'statements' => $statements
        ]);
    }

    /**
     * @param StandardFormStore $request
     *
     * @return JsonResponse
     */
    public function store(StandardFormStore $request): JsonResponse
    {
        $form = $this->standardForm->create($request->validatedOnly());
        if ($request->has('statements')) {
            $statements = $request->get('statements');
            foreach ($statements as $key => $statement) {
                if (array_key_exists('id', $statement)) {
                    $standardStatement = $this->standardStatement->find($statement['id']);
                    $standardStatement->update($statement);
                } else {
                    $statement['company_id'] = auth()->user()->company_id;
                    $this->standardStatement->create($statement);
                }
            }
        }
        return $this->respond(['message' => 'Form successfully created', 'form' => $form]);
    }

    /**
     * @param StatementStore $request
     *
     * @return JsonResponse
     */
    public function statementStore(StatementStore $request): JsonResponse
    {
        $statement = $this->standardStatement->create([
            'form_id' => $request->get('form_id'),
            'company_id' => auth()->user()->company_id,
            'title' => $request->get('title'),
            'statement' => ''
        ]);

        return $this->respond(['message' => 'Statement successfully created', 'statement' => $statement]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function statementDelete(int $id): JsonResponse
    {
        $this->standardStatement->findOrFail($id)->delete();

        return $this->respond(['message' => 'Statement successfully deleted']);
    }

    /**
     * @param StandardFormUpdate $request
     *
     * @return JsonResponse
     */
    public function update(StandardFormUpdate $request): JsonResponse
    {
        $form = $this->standardForm->find($request->input('id'));
        $form->update($request->validatedOnly());
        if ($request->has('statements')) {
            $statements = $request->get('statements');
            foreach ($statements as $statement) {
                $standardStatement = $this->standardStatement->find($statement['id']);
                $standardStatement->update($statement);
            }
        }

        return $this->respond(['message' => 'Form successfully updated', 'form' => $form]);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->standardForm->findOrFail($id)->delete();

        return $this->respond(['message' => 'Form successfully deleted']);
    }

    public function preview(Request $request, string $token)
    {
        $this->jwtAuth->setToken($token);
        try {
            $user = $this->jwtAuth->authenticate();
            $companyid = $user->company_id;
            $company = $user->company;
        } catch (TokenInvalidException $e) {
            return view('errors.401');
        } catch (TokenExpiredException $e) {
            return view('errors.401');
        }

        $formids = $request->all();
        $c_name = $company->name;
        $c_street = $company->street;
        $c_address = $company->city . ' ' . $company->state . ' ' . $company->zip;
        $c_phone = $company->phone;
        $c_email = $company->email;
        $c_imgfile = "../resources/frontend/src/assets/fallback-logo.jpg";
        $c_headerdata = "$c_street\n$c_address\n$c_phone\n$c_email";
        //--------------------create PDF document----------------------//
        // set document information
        PDF::SetTitle('dryforms-standards-print');
        PDF::setPrintHeader(false);
        // Page footer
        PDF::setFooterCallback(function($cpdf) {
            // Position at 15 mm from bottom
            $cpdf->SetY(-15);
            // Set font
            $cpdf->SetFont('helvetica', 'I', 8);
            // Page number
            $cpdf->Cell(0, 10, 'Page '.$cpdf->getAliasNumPage().'/'.$cpdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        });
        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
        }
        // IMPORTANT: disable font subsetting to allow users editing the document
        PDF::setFontSubsetting(false);
        foreach ($formids as $key => $value) {
            if ($value == 2){
                $this->print_header($c_imgfile, $c_name, $c_headerdata);
                $this->print_project_scope($value, $c_imgfile, $c_name, $c_headerdata);
                // exit;
            }
            else if (in_array($value, [4, 5, 6, 9, 10, 11])) {
                $this->print_header($c_imgfile, $c_name, $c_headerdata);
                $this->print_main_form($value);
            }
        }
        PDF::Output("dryforms_standards.pdf",'D');
    }

    public function print_header($c_imgfile, $c_name, $c_headerdata)
    {
        // add a page
        PDF::AddPage();

        PDF::Image($c_imgfile, 15, 10, 50, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        PDF::SetFont('helvetica', 'B', 15);
        // Title
        PDF::SetY(12);
        PDF::SetX(20);
        PDF::Cell(0, 15, $c_name, 0, false, 'R', 0, '', 0, false, 'M', 'M');

        PDF::SetFont('helvetica', 'B', 10);
        PDF::SetY(16);
        PDF::SetX(-85);
        PDF::MultiCell(70, 150, $c_headerdata, 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'T', false);

        PDF::SetY(45);
        PDF::Cell(180, 0, '', 'T', 0, 'C');
    }

    public function print_main_form($form_id)
    {
        $form = $this->standardForm->where('form_id', $form_id);
        if ($form->count() == 0) {
            $form = $this->defaultFormData->where('form_id', $form_id)->first();
        } else {
            $form = $form->first();
        }
        $standardsStatements = $this->standardStatement->where('form_id', $form_id);
        if ($standardsStatements->count() == 0) {
            $standardsStatements = $this->defaultStatement
	        	->where('form_id', $form_id)
	        	->get();
        } else {
            $standardsStatements = $standardsStatements->get();
        }

        PDF::SetY(37);
        PDF::SetFont('helvetica', 'BI', 15);
        PDF::Cell(0, 5, $form['title'], 0, 1, 'C');
        // statement
        foreach ($standardsStatements as $key => $standardsStatement) {
            PDF::SetXY(15, PDF::GetY() + 6);
            PDF::SetFont('helvetica', '', 10, '', false);
            PDF::Cell(0, 6, $standardsStatement->title);
            PDF::SetXY(25, PDF::GetY() + 6);
            PDF::writeHtml($standardsStatement->statement, 1, 1, 0, 'L');
            PDF::SetXY(15, PDF::GetY() + 6);
            PDF::Line(15, PDF::GetY(), PDF::getPageWidth() - 15, PDF::GetY());
        }

        PDF::SetXY(15, PDF::GetY() + 6);
        PDF::SetFont('helvetica', '', 10, '', false);
        PDF::Cell(0, 6, 'Additional notes:');
        PDF::LinearGradient(15, PDF::GetY() + 6, PDF::getPageWidth() - 30, 30, [208, 208, 208], [208, 208, 208], [0, 0, 1, 0]);

        PDF::SetXY(15, PDF::GetY() + 42);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell(30, 6, 'Insured:');
        PDF::LinearGradient(15, PDF::GetY() + 8, (PDF::getPageWidth() - 50) / 3, 6, [208, 208, 208], [208, 208, 208], [0, 0, 1, 0]);
        PDF::SetX(15 + (PDF::getPageWidth() - 50) / 3 + 10);
        PDF::Cell(30, 6, 'Signature:');
        PDF::LinearGradient(15 + (PDF::getPageWidth() - 50) / 3 + 10, PDF::GetY() + 8, (PDF::getPageWidth() - 50) / 3, 6, [208, 208, 208], [208, 208, 208], [0, 0, 1, 0]);
        PDF::SetX(15 + (PDF::getPageWidth() - 50) / 3 * 2 + 20);
        PDF::Cell(30, 6, 'Date:');
        PDF::LinearGradient(15 + (PDF::getPageWidth() - 50) / 3 * 2 + 20, PDF::GetY() + 8, (PDF::getPageWidth() - 50) / 3, 6, [208, 208, 208], [208, 208, 208], [0, 0, 1, 0]);

        PDF::SetXY(15, PDF::GetY() + 16);
        PDF::writeHtml($form['footer_text'], 1, 1, 0, 'L');
    }

    public function print_project_scope($form_id, $c_imgfile, $c_name, $c_headerdata)
    {
        $form = $this->standardForm->where('form_id', $form_id);
        if ($form->count() == 0) {
            $form = $this->defaultFormData->where('form_id', $form_id)->first();
        } else {
            $form = $form->first();
        }

        if ($this->standard_scope->count() == 0) {
    		$default_scopes = $this->default_scope->get()->toArray();
    		foreach ($default_scopes as $key => $scope) {
	            $scope['company_id'] = auth()->user()->company_id;
		        $this->standard_scope->create($scope);
	        }
        }
        $scopes = $this->standard_scope
                ->orderBy('no')
                ->orderBy('page')
                ->get()
                ->groupBy('page')
                ->toArray();
        PDF::SetY(37);
        PDF::SetFont('helvetica', 'BI', 15);
        PDF::Cell(0, 5, $form['title'], 0, 1, 'C');
        $height = PDF::getPageHeight();
        // project scope
        foreach ($scopes as $key => $page) {
            if ($key == 0) continue;
            PDF::SetXY(PDF::getPageWidth() - 36, PDF::GetY() + 6);
            PDF::SetFont('helvetica', '', 10, '', false);
            PDF::Cell(21, 5, 'Page'. $key, 0, 1, 'C');
            PDF::Line(PDF::getPageWidth() - 36, PDF::GetY(), PDF::getPageWidth() - 15, PDF::GetY());
            PDF::SetY(PDF::GetY() + 3);
            foreach ($page as $key => $scope) {
                if ($key >= ceil(count($page) / 2.0)) break;
                $y = PDF::GetY();
                if ($y >= $height - 30) {
                    $this->print_header($c_imgfile, $c_name, $c_headerdata);
                    $y = 45;
                }
                PDF::SetXY(15, $y);
                if ($scope['is_header'] == 1) {
                    PDF::SetFillColor(208, 208, 208);
                    PDF::SetFont('helvetica', 'B', 10, '', false);
                    PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, 'X', 1, 1, 'C', 1);
                } else {
                    PDF::SetFillColor(255, 255, 255);
                    PDF::SetFont('helvetica', '', 10, '', false);
                    $check = $scope['selected'] ? 'X' : '';
                    PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, $check, 1, 1, 'C', 1);
                }

                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 11, 5, $scope['service'], 1, 1, 'C', 1);
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 12, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $scope['uom'], 1, 1, 'C', 1);
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 16, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $scope['qty'], 1, 1, 'C', 1);
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 23, $y);
                if ($page[$key + ceil(count($page) / 2.0)]['is_header'] == 1) {
                    PDF::SetFillColor(208, 208, 208);
                    PDF::SetFont('helvetica', 'B', 10, '', false);
                    PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, 'X', 1, 1, 'C', 1);
                } else {
                    PDF::SetFillColor(255, 255, 255);
                    PDF::SetFont('helvetica', '', 10, '', false);
                    $check = $page[$key + ceil(count($page) / 2.0)]['selected'] ? 'X' : '';
                    PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, $check, 1, 1, 'C', 1);
                }
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 24, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 11, 5, $page[$key + ceil(count($page) / 2.0)]['service'], 1, 1, 'C', 1);
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 35, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $page[$key + ceil(count($page) / 2.0)]['uom'], 1, 1, 'C', 1);
                PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 39, $y);
                PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $page[$key + ceil(count($page) / 2.0)]['qty'], 1, 1, 'C', 1);
            }
        }

        PDF::SetXY(PDF::getPageWidth() - 36, PDF::GetY() + 6);
        PDF::SetFont('helvetica', '', 10, '', false);
        PDF::Cell(21, 5, 'Misc Page', 0, 1, 'C');
        PDF::Line(PDF::getPageWidth() - 36, PDF::GetY(), PDF::getPageWidth() - 15, PDF::GetY());
        PDF::SetY(PDF::GetY() + 3);
        $page = $scopes[0];
        foreach ($page as $key => $scope) {
            if ($key >= ceil(count($page) / 2.0)) break;
            $y = PDF::GetY();
            if ($y >= $height - 30) {
                $this->print_header($c_imgfile, $c_name, $c_headerdata);
                $y = 45;
            }
            PDF::SetXY(15, $y);
            if ($scope['is_header'] == 1) {
                PDF::SetFillColor(208, 208, 208);
                PDF::SetFont('helvetica', 'B', 10, '', false);
                PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, 'X', 1, 1, 'C', 1);
            } else {
                PDF::SetFillColor(255, 255, 255);
                PDF::SetFont('helvetica', '', 10, '', false);
                $check = $scope['selected'] ? 'X' : '';
                PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, $check, 1, 1, 'C', 1);
            }

            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 11, 5, $scope['service'], 1, 1, 'C', 1);
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 12, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $scope['uom'], 1, 1, 'C', 1);
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 16, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $scope['qty'], 1, 1, 'C', 1);
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 23, $y);
            if ($page[$key + ceil(count($page) / 2.0)]['is_header'] == 1) {
                PDF::SetFillColor(208, 208, 208);
                PDF::SetFont('helvetica', 'B', 10, '', false);
                PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, 'X', 1, 1, 'C', 1);
            } else {
                PDF::SetFillColor(255, 255, 255);
                PDF::SetFont('helvetica', '', 10, '', false);
                $check = $page[$key + ceil(count($page) / 2.0)]['selected'] ? 'X' : '';
                PDF::Cell((PDF::getPageWidth() - 30) / 43, 5, $check, 1, 1, 'C', 1);
            }
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 24, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 11, 5, $page[$key + ceil(count($page) / 2.0)]['service'], 1, 1, 'C', 1);
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 35, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $page[$key + ceil(count($page) / 2.0)]['uom'], 1, 1, 'C', 1);
            PDF::SetXY(15 + (PDF::getPageWidth() - 30) / 43 * 39, $y);
            PDF::Cell((PDF::getPageWidth() - 30) / 43 * 4, 5, $page[$key + ceil(count($page) / 2.0)]['qty'], 1, 1, 'C', 1);
        }
    }
}

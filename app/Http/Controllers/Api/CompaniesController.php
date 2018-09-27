<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Companies\RemoveLogoRequest;
use App\Http\Requests\Companies\StoreLogoRequest;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use App\Http\Requests\Companies\CompanyStore;
use App\Http\Requests\Companies\CompanyUpdate;
use App\Models\Company;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\JWTAuth;

class CompaniesController extends ApiController
{
    /**
     * @var Company
     */
    private $company;

    /**
     * @var User
     */
    private $user;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * CompaniesController constructor.
     * @param Company $company
     * @param User $user
     * @param JWTAuth $jwtAuth
     */
    public function __construct(Company $company, User $user, JWTAuth $jwtAuth)
    {
        $this->company = $company;
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $companies = $this->company->paginate(20);

        return $this->respond($companies);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $company = $this->company->findOrFail($id);

        return $this->respond($company);
    }

    /**
     * @param CompanyStore $request
     *
     * @return JsonResponse
     */
    public function store(CompanyStore $request): JsonResponse
    {
        $user = auth()->user();
        $company = $this->company->create([
            'name' => $request->input('name'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'user_id' => $user->id
        ]);

        $user->company_id = $company->id;
        $user->save();

        return $this->respond([
            'message' => 'Company successfully created',
            'company' => $company,
            'user' => $user
        ]);
    }

    /**
     * @param CompanyUpdate $request
     * @return JsonResponse
     */
    public function update(CompanyUpdate $request): JsonResponse
    {
        $company = $this->company->find($request->route('company'));
        $company->update([
            'name' => $request->input('name'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email')
        ]);

        return $this->respond([
            'message' => 'Company details successfully updated',
            'company' => $company
        ]);
    }

    /**
     * @param StoreLogoRequest $request
     * @param FilesystemManager $filesystemManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLogo(StoreLogoRequest $request, FilesystemManager $filesystemManager): JsonResponse
    {
        $company = $this->company->findOrFail($request->route('company'));
        $logoFile = $request->file('logo');
        $filename = str_random() . '.' . $logoFile->getClientOriginalExtension();
        $path = $filesystemManager->disk('public')->putFileAs('logos', $logoFile, $filename);

        $company->logo = $path;
        $company->save();

        return response()->json([
            'company' => $company
        ]);
    }

    /**
     * @param RemoveLogoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeLogo(RemoveLogoRequest $request): JsonResponse
    {
        $company = $this->company->findOrFail($request->route('company'));
        $company->logo = null;
        $company->save();

        return response()->json([
            'message' => 'Logo successfully removed',
            'company' => $company
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->company->findOrFail($id)->delete();

        return $this->respond(['message' => 'Company successfully deleted']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function dropboxAuth(Request $request)
    {
        return view('auth.dropbox-auth');
    }
}
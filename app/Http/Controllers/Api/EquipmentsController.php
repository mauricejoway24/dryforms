<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Equipments\EquipmentIndex;
use App\Http\Requests\Equipments\EquipmentStore;
use App\Http\Requests\Equipments\EquipmentUpdate;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\EquipmentModel;
use App\Models\Status;
use App\Models\Team;
use App\Services\QueryBuilder;
use App\Services\QueryBuilders\EquipmentQueryBuilder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Config\Repository as Config;

class EquipmentsController extends ApiController
{
    /**
     * @var Equipment
     */
    private $equipment;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Team
     */
    private $team;

    /**
     * @var EquipmentModel
     */
    private $model;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Config
     */
    private $config;

    /**
     * EquipmentsController constructor.
     *
     * @param Equipment $equipment
     * @param EquipmentModel $model
     * @param Status $status
     * @param Category $category
     * @param Team $team
     * @param Config $config
     */
    public function __construct(
        Equipment $equipment,
        EquipmentModel $model,
        Status $status,
        Category $category,
        Team $team,
        Config $config
    ) {
        $this->equipment = $equipment;
        $this->model = $model;
        $this->status = $status;
        $this->category = $category;
        $this->team = $team;
        $this->config = $config;
    }

    /**
     * @param EquipmentIndex $request
     *
     * @return JsonResponse
     */
    public function index(EquipmentIndex $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $equipments = $this->equipment->with([
            'team',
            'status',
            'model',
            'model.category'
        ]);
        $queryBuilder = new EquipmentQueryBuilder();
        $equipments = $queryBuilder->setQuery($equipments)->setQueryParams($queryParams);

        $equipments = $equipments->paginate($request->get('per_page'));

        return $this->respond($equipments);
    }

    /**
     * @param int $id Model id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $equipment = $this->equipment->with([
            'model.category',
            'team',
            'status'
        ])->findOrFail($id);

        return $this->respond($equipment);
    }

    /**
     * @param array $serials
     * @param int $category_id
     * @param string $categoryPrefix
     *
     * @return array
     */
    private function validateSerials($serials, $category_id, $categoryPrefix)
    {
        $responseData = [
            'exists'=> [],
            'nonexistences'=> []
        ];

        foreach ($serials as $key => $serial) {
            $serialNumber = $categoryPrefix . str_pad(intval($serial['value'], 10), $this->config->get('constants.equipment.serial_length'), "0", STR_PAD_LEFT);
            if ($this->equipment
                ->with(['model'])
                ->where('serial', $serialNumber)
                ->whereHas('model', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                })->exists()) {
                array_push($responseData['exists'], $serial);
            } else {
                array_push($responseData['nonexistences'], $serial);
            }
        }
        return $responseData;
    }

    /**
     * @param EquipmentStore $request
     *
     * @return JsonResponse
     */
    public function store(EquipmentStore $request): JsonResponse
    {
        $categoryPrefix = $this->category->find($request->get('category_id'))->prefix;
        $categoryPrefix = strlen($categoryPrefix) > 0 ? $categoryPrefix . " " : "";
        if ($request->get('auto_assign') === "yes") {
            $queryBuilder = new EquipmentQueryBuilder();
            $maxSerial = $queryBuilder->setQuery($this->equipment->query())->getMaxSerialQuery($categoryPrefix, $request->get('category_id'));
            $maxSerial = ($maxSerial->count() > 0) ? $maxSerial->first()->max_serial: 0;
            $serial = $maxSerial + 1;
            $equipments = [];
            for ($index=0; $index < $request->get('quantity'); $index++) {
                $equipment = $this->equipment->create([
                    'model_id' => $request->get('model_id'),
                    'team_id' => $request->get('team_id'),
                    'serial' => $categoryPrefix. str_pad($serial, $this->config->get('constants.equipment.serial_length'), "0", STR_PAD_LEFT),
                    'status_id' => $request->get('status_id'),
                    'company_id' => $request->get('company_id'),
                ]);
                $serial++;
                $equipment->load(['model.category', 'status', 'team']);
                array_push($equipments, $equipment);
            }
            return $this->respond(['message' => 'Equipments successfully created', 'equipment' => $equipments]);
        }
        $valRet = $this->validateSerials($request->get('serials'), $request->get('category_id'), $categoryPrefix);
        if (empty($valRet['exists'])) {
            $equipments = [];
            foreach ($valRet['nonexistences'] as $key => $serial) {
                $equipment = $this->equipment->create([
                    'model_id' => $request->get('model_id'),
                    'team_id' => $request->get('team_id'),
                    'serial' => $categoryPrefix. str_pad(intval($serial['value'],10), $this->config->get('constants.equipment.serial_length'), "0", STR_PAD_LEFT),
                    'status_id' => $request->get('status_id'),
                    'company_id' => $request->get('company_id'),
                ]);
                $equipment->load(['model.category', 'status', 'team']);
                array_push($equipments, $equipment);
            }
            return $this->respond(['message' => 'Equipments successfully created', 'equipment' => $equipments]);
        } else {
            return $this->respond(['message' => 'error', 'validate' => ['serials' => $valRet]]);
        }
    }

    /**
     * @param EquipmentUpdate $request
     *
     * @return JsonResponse
     */
    public function update(EquipmentUpdate $request)
    {
        $equipment = $this->equipment->find($request->input('equipment_id'));
        $request_params = $request->validated();
        if (array_key_exists('category_id', $request_params) && array_key_exists('serial', $request_params)) {
            $categoryPrefix = $this->category->find($request_params['category_id'])->prefix;
            $categoryPrefix = strlen($categoryPrefix) > 0 ? $categoryPrefix. " " : "";
            $serials = [['value'=>intval($request_params['serial'], 10)]];
            $valRet = $this->validateSerials($serials, $request_params['category_id'], $categoryPrefix);
            if (empty($valRet['exists'])) {
                $request_params['serial'] = $categoryPrefix. str_pad(intval($request_params['serial'],10), $this->config->get('constants.equipment.serial_length'), "0", STR_PAD_LEFT);
                unset($request_params['category_id']);
            } else {
                return $this->respond(['message' => 'exist']);
            }
        }
        if (array_key_exists('category_id', $request_params)) {
            unset($request_params['category_id']);
        }
        if (!isset($request_params['project_id'])) {
            $request_params['project_id'] = null;
        }
        $equipment->update($request_params);
        $equipment->load(['model.category', 'status', 'team']);
        return $this->respond(['message' => 'Equipment successfully updated', 'equipment_id' => $equipment]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->equipment->findOrFail($id)->delete();

        return $this->respond(['message' => 'Equipment successfully deleted']);
    }

    /**
     * Check if Serial exists
     *
     * @param int $serial
     * @param int $categoryId
     *
     * @return  JsonResponse
     */
    public function validateSerial($serial, $categoryId): JsonResponse
    {
        if (!ctype_digit($serial)) return $this->respond(['message' => 'serial is not numeric']);
        $categoryPrefix = $this->category->find($categoryId)->prefix;
        $categoryPrefix = strlen($categoryPrefix) > 0 ? $categoryPrefix. " " : "";
        $serials = [['value'=>intval($serial, 10)]];
        $valRet = $this->validateSerials($serials, $categoryId, $categoryPrefix);
        if (empty($valRet['exists'])) {
            return $this->respond(['message' => 'nonexistence']);
        }
        return $this->respond(['message' => 'exist']);
    }
}
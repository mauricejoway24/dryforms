<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ReviewLink;
use App\Http\Requests\ReviewLinks\ReviewLinkIndex;
use App\Http\Requests\ReviewLinks\ReviewLinkStore;
use App\Http\Requests\ReviewLinks\ReviewLinkUpdate;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReviewLinksController extends ApiController
{
    /**
     * @var ReviewLink
     */
    private $reviewLink;

    /**
     * ReviewLinksController constructor.
     *
     * @param ReviewLink $reviewLink
     */
    public function __construct(ReviewLink $reviewLink)
    {
        $this->reviewLink = $reviewLink;
    }

    /**
     * @param ReviewLinkIndex $request
     *
     * @return JsonResponse
     */
    public function index(ReviewLinkIndex $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $reviewLink = $this->reviewLink;
        if (isset($queryParams['sort_by'])) {
            $sort_type = isset($queryParams['sort_type']) ? $queryParams['sort_type'] : 'asc';
            $sort_by = $queryParams['sort_by'];
            $reviewLink->orderBy($sort_by, $sort_type);
        }
        $reviewLinks = $reviewLink->paginate($request->get('per_page'));        
        return $this->respond([
            'reviewLinks' => $reviewLinks,
            'activateCount' => $this->reviewLink->where('activate', 1)->count()
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $reviewLink = $this->reviewLink->findOrFail($id);

        return $this->respond($reviewLink);
    }

    /**
     * @param ReviewLinkStore $request
     *
     * @return JsonResponse
     */
    public function store(ReviewLinkStore $request): JsonResponse
    {
        $activate = $request->get('activate');
        if ($this->reviewLink->where('activate', 1)->count() >= 3) {
            $activate = 0;
        }
        $reviewLink = $this->reviewLink->create([
            'url' => $request->get('url'),
            'activate' => $activate,
            'company_id' => auth()->user()->company_id,
        ]);

        return $this->respond(['message' => 'ReviewLink successfully created', 'reviewLink' => $reviewLink]);
    }

    /**
     * @param ReviewLinkUpdate $request
     *
     * @return JsonResponse
     */
    public function update(int $id, ReviewLinkUpdate $request): JsonResponse
    {
        $reviewLink = $this->reviewLink->findOrFail($request->input('id'));
        $reviewLink->update([
            'url' => $request->get('url'),
            'activate' => $request->get('activate'),
            'company_id' => auth()->user()->company_id,
        ]);
        if ($this->reviewLink->where('activate', 1)->count() > 3) {
            $reviewLink->update([
                'activate' => 0
            ]);
            $reviewLink['activate'] = 0;
        }
        return $this->respond(['message' => 'ReviewLink successfully updated', 'reviewLink' => $reviewLink]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->reviewLink->findOrFail($id)->delete();

        return $this->respond(['message' => 'ReviewLink successfully deleted']);
    }
}

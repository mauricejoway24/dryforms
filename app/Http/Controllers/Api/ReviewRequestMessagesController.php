<?php

namespace App\Http\Controllers\Api;

use App\Models\ReviewRequestMessage;
use App\Http\Requests\ReviewRequestMessages\ReviewRequestMessageIndex;
use App\Http\Requests\ReviewRequestMessages\ReviewRequestMessageUpdate;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReviewRequestMessagesController extends ApiController
{
    /**
     * @var ReviewRequestMessage
     */
    private $reviewRequestMessage;

    /**
     * ReviewRequestMessagesController constructor.
     *
     * @param ReviewRequestMessage $reviewRequestMessage
     */
    public function __construct(ReviewRequestMessage $reviewRequestMessage)
    {
        $this->reviewRequestMessage = $reviewRequestMessage;
    }

    /**
     * @param ReviewRequestMessageIndex $request
     *
     * @return JsonResponse
     */
    public function index(ReviewRequestMessageIndex $request): JsonResponse
    {
        if ($this->reviewRequestMessage->count() == 0) {
            $this->reviewRequestMessage->create([
                'company_id' => auth()->user()->company_id,
                'message' => "We appreciate your business and hope we did a good job. <br> If you don't mind, could you please lease us a review at 1 or all of the sites below as we're trying to build our online reputation <br> <br> <a href='%1'>%1</a> <br> <a href='%2'>%2</a> <br> <a href='%3'>%3</a> <br>"
            ]);
        }
        return $this->respond($this->reviewRequestMessage->first());
    }

    /**
     * @param ReviewRequestMessageUpdate $request
     *
     * @return JsonResponse
     */
    public function update(int $id, ReviewRequestMessageUpdate $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $reviewRequestMessage = $this->reviewRequestMessage
            ->where('company_id', $id)
            ->update([
                'company_id' => auth()->user()->company_id,
                'message' => $queryParams['message']
            ]);

        return $this->respond(['message' => 'Review Request Message successfully updated', 'reviewRequestMessage' => $queryParams]);
    }
}

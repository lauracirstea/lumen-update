<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /** @const string */
    const RESPONSE_SUCCESS = 'success';

    /** @const string */
    const RESPONSE_ERROR = 'error';

    /** @var null */
    protected $data = null;

    /** @var null */
    protected $errorMessage = null;

    /** @var null */
    protected $errorCode = null;

    /** @var string */
    protected $responseType;

    /** @var BaseService */
    protected $baseService;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->baseService = new BaseService();
    }

    /**
     * Build the response.
     *
     * @return JsonResponse
     */
    private function returnResponse()
    {
        $response = [
            'responseType' => $this->responseType,
            'data' => $this->data,
            'errorMessage' => $this->errorMessage,
            'errorCode' => $this->errorCode
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * @param $errorMessage
     * @param $errorCode
     *
     * @return JsonResponse
     */
    protected function returnError($errorMessage, $errorCode)
    {
        $this->responseType = self::RESPONSE_ERROR;
        $this->errorMessage = $errorMessage;
        $this->errorCode = $errorCode;

        return $this->returnResponse();
    }

    /**
     * Return success.
     *
     * @param null $data
     * @param null $pagination
     *
     * @return JsonResponse
     */
    protected function returnSuccess($data = null, $pagination = null)
    {
        $this->responseType = self::RESPONSE_SUCCESS;
        $this->data = [
            'result' => $data,
            'pagination' => $pagination
        ];

        return $this->returnResponse();
    }

    /**
     * Get pagination offset and limit
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getPaginationParams(Request $request)
    {
        $limit = 10;
        if ($request->has('limit')) {
            $requestLimit = (int)$request->get('limit');

            if ($requestLimit > 0) {
                $limit = $requestLimit;
            }
        }

        $offset = 0;
        $page = 1;

        if ($request->has('page')) {
            $requestPage = (int)$request->get('page');

            if ($requestPage > 1) {
                $page = $requestPage;
            }

            $offset = ($page - 1) * $limit;
        }

        return [
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit
        ];
    }

    /**
     * Apply sort params
     *
     * @param Request $request
     * @param Builder $builder
     *
     * @return Builder
     */
    protected function applySortParams(Request $request, Builder $builder)
    {
        if ($request->has('sortColumn') || $request->has('sortOrder')) {
            $sortColumn = strtolower($request->get('sortColumn', 'id'));
            $sortOrder = strtolower($request->get('sortOrder', 'asc'));

            if (in_array($sortColumn, $builder->getModel()->getVisible()) && in_array($sortOrder, ['asc', 'desc'])) {
                return $builder->orderBy($sortColumn, $sortOrder);
            }
        }

        return $builder;
    }

    /**
     * Get pagination data
     *
     * @param Builder $builder
     * @param $page
     * @param $limit
     *
     * @return array
     */
    protected function getPaginationData(Builder $builder, $page, $limit)
    {
        $totalEntries = $builder->count();

        $totalPages = ceil($totalEntries / $limit);

        return [
            'currentPage' => $page > $totalPages ? $totalPages : $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalEntries' => $totalEntries
        ];
    }
}

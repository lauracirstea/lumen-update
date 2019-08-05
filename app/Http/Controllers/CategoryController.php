<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorCodes;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /** @var CategoryService */
    protected $categoryService;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->categoryService = new CategoryService();
    }

    /**
     * Create a category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->categoryService->validateCreateRequest($request);

            if (!$validator->passes()) {
                return $this->returnError($validator->messages(), ErrorCodes::REQUEST_ERROR);
            }

            $category = new Category();

            if ($request->has('parent_id')) {
                $parentCategory = Category::where('id', $request->get('parent_id'))
                    ->where('parent_id', Category::MAIN_CATEGORY)
                    ->first();

                if (!$parentCategory) {
                    return $this->returnError('errors.parent_id.invalid', ErrorCodes::REQUEST_ERROR);
                }

                $category->parent_id = $parentCategory->id;
            } else {
                $category->parent_id = Category::MAIN_CATEGORY;
            }

            $category->name = $request->get('name');

            $category->save();

            return $this->returnSuccess($category);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Get all categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        try {
            $pagParams = $this->getPaginationParams($request);

            $categories = Category::where('id', '!=', null);

            $paginationData = $this->getPaginationData($categories, $pagParams['page'], $pagParams['limit']);

            $categories = $categories->offset($pagParams['offset'])->limit($pagParams['limit'])->get();

            return $this->returnSuccess($categories, $paginationData);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Get all categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subCategories()
    {
        try {
            $categories = Category::with('subCategories')
                ->where('parent_id', Category::MAIN_CATEGORY)
                ->get();

            return $this->returnSuccess($categories);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Get one category
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $category = Category::where('id', $id)->first();

            if (!$category) {
                return $this->returnError('errors.category.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            return $this->returnSuccess($category);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Update a category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $category = Category::where('id', $id)->first();

            if (!$category) {
                return $this->returnError('errors.category.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->categoryService->validateUpdateRequest($request);

            if (!$validator->passes()) {
                return $this->returnError($validator->messages(), ErrorCodes::REQUEST_ERROR);
            }

            if ($request->has('parent_id')) {
                $parentCategory = Category::where('id', $request->get('parent_id'))
                    ->where('parent_id', Category::MAIN_CATEGORY)
                    ->first();

                if (!$parentCategory) {
                    return $this->returnError('errors.parent_id.invalid', ErrorCodes::REQUEST_ERROR);
                }

                $category->parent_id = $parentCategory->id;
            } else {
                $category->parent_id = Category::MAIN_CATEGORY;
            }

            $category->name = $request->get('name');

            $category->save();

            return $this->returnSuccess($category);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Delete a category
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $category = Category::where('id', $id)->first();

            if (!$category) {
                return $this->returnError('errors.category.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            $category->delete();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }
}

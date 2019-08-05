<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorCodes;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /** @var ProductService */
    protected $productService;

    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->productService = new ProductService();
    }

    /**
     * Create a product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aa(Request $request)
    {
        $rules = [
            'name'     => 'required|string|min:3|max:191',
            'email'    => 'required|email|min:3|max:191',
            'password' => 'nullable|string|min:5|max:191',
            'image'    => 'nullable|image|max:1999', //formats: jpeg, png, bmp, gif, svg
        ];
        $request->validate($rules);
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->hasFile('image')) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $filename = uniqid() . '.' . $ext;
            $image->storeAs('public/pics', $filename);
            Storage::delete("public/pics/{$user->image}");
            $user->image = $filename;
        }
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()
            ->route('profile.index')
            ->with('status', 'Your profile has been updated!');
    }

    public function create(Request $request)
    {
        try {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->productService->validateCreateRequest($request);

            if (!$validator->passes()) {
                return $this->returnError($validator->messages(), ErrorCodes::REQUEST_ERROR);
            }

            $product = new Product($request->all());

            if ($request->hasFile('photo')) {
                $image = Image::make( $request->file('photo'));
                $imagePath = base_path('public/uploads/').$request->file('photo')->getFilename(). '.' .$request->file('photo')->getClientOriginalExtension();
                File::makeDirectory(base_path('public/uploads/'), 0777, true, true);
                $image->save($imagePath);

                $product->photo = 'uploads/'.$request->file('photo')->getFilename(). '.' .$request->file('photo')->getClientOriginalExtension();
            }

            $numberOfSameCategories = Product::orderBy('category_id')->where("category_id", $request->category_id)->count();
            $salePrice = 0;

            if ($product->quantity >= 100) {
                $salePrice = $product->full_price - $product->full_price * 0.1;
            }
            if ($numberOfSameCategories > 2 && $product->quantity >= 100) {
                $salePrice = $salePrice + $salePrice * 0.05;
            } elseif ($numberOfSameCategories > 2 && $product->quantity < 100) {
                $salePrice =  $product->full_price + $product->full_price * 0.05;
            }

            if ($salePrice === 0) {
                $salePrice = $product->full_price;
            }

            $product->sale_price = round($salePrice, 2);
            $product->save();


            return $this->returnSuccess("The product has been added successfully", $product);
        } catch (\Exception $e) {
            dd($e);

            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        try {
            $pagParams = $this->getPaginationParams($request);

            $products = Product::where('id', '!=', null);

            $paginationData = $this->getPaginationData($products, $pagParams['page'], $pagParams['limit']);

            $products = $products->offset($pagParams['offset'])->limit($pagParams['limit'])->get();

            return $this->returnSuccess($products, $paginationData);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Get one product
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $product = Product::where('id', $id)->first();

            if (!$product) {
                return $this->returnError('errors.category.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            return $this->returnSuccess($product);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Update a product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $product = Product::where('id', $id)->first();

            if (!$product) {
                return $this->returnError('errors.product.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->productService->validateUpdateRequest($request);

            if (!$validator->passes()) {
                return $this->returnError($validator->messages(), ErrorCodes::REQUEST_ERROR);
            }

            $product->fill([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'full_price' => $request->full_price,
                'quantity' => $request->quantity,
            ]);

            if ($request->hasFile('photo')) {
                $image = Image::make( $request->file('photo'));
                $imagePath = base_path('public/uploads/').$request->file('photo')->getFilename(). '.' .$request->file('photo')->getClientOriginalExtension();
                File::makeDirectory(base_path('public/uploads/'), 0777, true, true);
                $image->save($imagePath);

                $product->photo = 'uploads/'.$request->file('photo')->getFilename(). '.' .$request->file('photo')->getClientOriginalExtension();
            }


            $numberOfSameCategories = Product::orderBy('category_id')->where("category_id", $request->category_id)->count();
            $salePrice = 0;

            if ($product->quantity >= 100) {
                $salePrice = $product->full_price - $product->full_price * 0.1;
            }
            if ($numberOfSameCategories > 2 && $product->quantity >= 100) {
                $salePrice = $salePrice + $salePrice * 0.05;
            } elseif ($numberOfSameCategories > 2 && $product->quantity < 100) {
                $salePrice =  $product->full_price + $product->full_price * 0.05;
            }

            if ($salePrice === 0) {
                $salePrice = $product->full_price;
            }

            $product->sale_price = round($salePrice, 2);
            $product->save();

            return $this->returnSuccess($product);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }

    /**
     * Delete a product
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $product = Product::where('id', $id)->first();

            if (!$product) {
                return $this->returnError('errors.category.not_found', ErrorCodes::NOT_FOUND_ERROR);
            }

            $product->delete();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), ErrorCodes::FRAMEWORK_ERROR);
        }
    }
}

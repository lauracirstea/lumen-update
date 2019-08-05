<?php


namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryService
 *
 * @package App\Services
 */
class ProductService
{
    public function validateCreateRequest(Request $request)
    {
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            'full_price' => 'required',
            'quantity' => 'required',
            'photo' => 'required',
            'description' => 'required'
        ];

        $messages = [
            'name.required' => 'errors.name.required',
            'category_id.required' => 'errors.category_id.required',
            'full_price.required' => 'errors.full_price.required',
            'quantity.required' => 'errors.quantity.required',
            'photo.required' => 'errors.photo.required',
            'description.required' => 'errors.description.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function validateUpdateRequest(Request $request)
    {
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            'full_price' => 'required',
            'quantity' => 'required',
            'photo' => 'required',
            'description' => 'required'
        ];

        $messages = [
            'name.required' => 'errors.name.required',
            'category_id.required' => 'errors.category_id.required',
            'full_price.required' => 'errors.full_price.required',
            'quantity.required' => 'errors.quantity.required',
            'photo.required' => 'errors.photo.required',
            'description.required' => 'errors.description.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}

<?php


namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryService
 *
 * @package App\Services
 */
class CategoryService
{
    public function validateCreateRequest(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $messages = [
            'name.required' => 'errors.name.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function validateUpdateRequest(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $messages = [
            'name.required' => 'errors.name.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}

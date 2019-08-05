<?php

namespace App\Http\Controllers;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * Get API version details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function version()
    {
        $apiDetails = [
            'version' => 'Lumen 5.8',
            'info' => 'It works!'
        ];

        return $this->returnSuccess($apiDetails);
    }
}

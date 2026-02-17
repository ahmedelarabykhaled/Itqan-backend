<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CustomersController extends Controller
{
    // get customer profile
    /**
     * @OA\Get(
     *      path="/api/customers/profile",
     *      tags={"Customers"},
     *      summary="Get customer profile",
     *      description="Get customer profile",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function getProfile(Request $request)
    {
        $customer = $request->user();
        return response()->json([
            'customer' => $customer,
        ], 200);
    }
}

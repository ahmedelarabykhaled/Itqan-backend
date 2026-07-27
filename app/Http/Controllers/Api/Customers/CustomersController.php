<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserMemorizedAyah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomersController extends Controller
{
    // get customer profile
    /**
     * @OA\Get(
     *      path="/api/v1/customers/profile",
     *      tags={"Customers"},
     *      summary="Get customer profile",
     *      description="Get customer profile",
     *      security={{"sanctum":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer profile fetched successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer profile fetched successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="avatar", type="string", nullable=true, example="customers/avatars/1/avatar.jpg")
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=401),
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      )
     * )
     */
    public function getProfile(Request $request)
    {
        $customer = $request->user();

        return ApiResponse::success(
            message: 'Customer profile fetched successfully',
            data: $customer,
            status: 200
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/customers/profile",
     *      tags={"Customers"},
     *      summary="Delete customer account",
     *      description="Permanently delete the authenticated customer account and all related data",
     *      security={{"sanctum":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer account deleted successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer account deleted successfully."),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=401),
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      )
     * )
     */
    public function deleteAccount(Request $request)
    {
        $customer = $request->user();
        $customerId = $customer->id;
        $email = $customer->email;

        DB::transaction(function () use ($customer, $email) {
            $customer->tokens()->delete();
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            UserMemorizedAyah::query()->where('user_id', $customer->id)->delete();
            $customer->delete();
        });

        Storage::disk('public')->deleteDirectory('customers/avatars/'.$customerId);

        return ApiResponse::success(
            message: __('customers.customer_deleted_successfully'),
            status: 200
        );
    }
}

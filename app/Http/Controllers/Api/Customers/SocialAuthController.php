<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Requests\SocialRegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // social register
    /**
     * @OA\Post(
     *      path="/api/customers/auth/social-register",
     *      tags={"Customers Social Authentication"},
     *      summary="Customer social register",
     *      description="Customer social register",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *              @OA\Property(property="avatar", type="string", example="avatar.jpg"),
     *              @OA\Property(property="provider", type="string", enum={"google", "facebook", "twitter"}, example="google"),
     *              @OA\Property(property="provider_id", type="string", example="123456"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Customer registered successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=201),
     *              @OA\Property(property="message", type="string", example="Customer registered successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="avatar", type="string", nullable=true, example="https://example.com/avatar.jpg"),
     *                  @OA\Property(property="provider", type="string", example="google"),
     *                  @OA\Property(property="provider_id", type="string", example="123456"),
     *                  @OA\Property(property="email_verified_at", type="string", format="date-time", example="2026-02-20T10:00:00.000000Z")
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=422),
     *              @OA\Property(property="message", type="string", example="Validation Error"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The provider field is required."))
     *          )
     *      )
     * )
     */
    public function socialRegister(SocialRegisterRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(10)),
            'gender' => $data['gender'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'provider' => $data['provider'],
            'provider_id' => $data['provider_id'],
            'email_verified_at' => now(),
        ]);

        return ApiResponse::success(
            message: __('customers.customer_registered_successfully'),
            data: $customer,
            status: 201
        );
    }

    // social login
    /**
     * @OA\Post(
     *      path="/api/customers/auth/social-login",
     *      tags={"Customers Social Authentication"},
     *      summary="Customer social login",
     *      description="Customer social login",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="provider", type="string", enum={"google", "facebook", "twitter"}, example="google"),
     *              @OA\Property(property="provider_id", type="string", example="123456"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer logged in successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer logged in successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="provider", type="string", example="google"),
     *                  @OA\Property(property="provider_id", type="string", example="123456"),
     *                  @OA\Property(property="token", type="string", example="1|xYzAbCdEf123")
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Customer not found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="Customer not found"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Customer not found"))
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=422),
     *              @OA\Property(property="message", type="string", example="Validation Error"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The provider_id field is required."))
     *          )
     *      )
     * )
     */
    public function socialLogin(SocialLoginRequest $request)
    {
        $customer = Customer::where('provider', $request->provider)
            ->where('provider_id', $request->provider_id)
            ->where('email', $request->email)
            ->first();

        if (! $customer) {
            return ApiResponse::error(
                message: __('customers.customer_not_found'),
                errors: [
                    __('customers.customer_not_found'),
                ],
                status: 404
            );
        }
        $token = $customer->createToken('auth-token')->plainTextToken;
        $customer->token = $token;

        return ApiResponse::success(
            message: __('customers.customer_logged_in_successfully'),
            data: $customer,
            status: 200
        );
    }
}

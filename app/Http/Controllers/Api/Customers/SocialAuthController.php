<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialRegisterRequest;
use App\Http\Requests\SocialLoginRequest;
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *              @OA\Property(property="avatar", type="string", example="avatar.jpg"),
     *              @OA\Property(property="provider", type="string", enum={"google", "facebook", "twitter"}, example="google"),
     *              @OA\Property(property="provider_id", type="string", example="123456"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
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

        return response()->json([
            'message' => __('customers.customer_registered_successfully'),
            'customer' => $customer,
        ], 201);
    }
    // social login
    /**
     * @OA\Post(
     *      path="/api/customers/auth/social-login",
     *      tags={"Customers Social Authentication"},
     *      summary="Customer social login",
     *      description="Customer social login",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="provider", type="string", enum={"google", "facebook", "twitter"}, example="google"),
     *              @OA\Property(property="provider_id", type="string", example="123456"),
     *          )  
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
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
            return response()->json([
                'message' => __('customers.customer_not_found'),
            ], 404);
        }
        return response()->json([
            'message' => __('customers.customer_logged_in_successfully'),
            'customer' => $customer,
            'token' => $customer->createToken('auth-token')->plainTextToken,
        ], 200);
    }
}

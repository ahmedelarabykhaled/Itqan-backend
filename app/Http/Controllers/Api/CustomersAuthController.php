<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLogin;
use App\Http\Requests\CustomerRegister;
use App\Http\Requests\CustomerUpdate;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class CustomersAuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/customers/auth/login",
     *      tags={"Customers Authentication"},
     *      summary="Customer login",
     *      description="Customer login",
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="[EMAIL_ADDRESS]"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer logged in successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Customer logged in successfully"),
     *              @OA\Property(property="customer", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="[EMAIL_ADDRESS]"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="avatar", type="string", example="avatar.jpg"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2022-01-01T00:00:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2022-01-01T00:00:00.000000Z"),
     *              ),
     *              @OA\Property(property="token", type="string", example="auth-token"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Bad Request"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="email", type="array", @OA\Items(example="The email field is required.")),
     *                  @OA\Property(property="password", type="array", @OA\Items(example="The password field is required.")),
     *              )
     *          )
     *      )
     * )
     */
    public function login(CustomerLogin $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => __('customers.customer_not_found'),
            ], 404);
        }

        if (! $customer->email_verified_at) {
            return response()->json([
                'message' => __('customers.customer_not_verified'),
            ], 401);
        }

        $token = $customer->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => __('customers.customer_logged_in_successfully'),
            'customer' => $customer,
            'token' => $token,
        ], 200);
    }

    // register
    /**
     * @OA\Post(
     *      path="/api/customers/auth/register",
     *      tags={"Customers Authentication"},
     *      summary="Customer register",
     *      description="Customer register",
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "gender"},
     *
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer registered successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Customer registered successfully"),
     *              @OA\Property(property="customer", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2022-01-01T00:00:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2022-01-01T00:00:00.000000Z"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Bad Request"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(example="The name field is required.")),
     *                  @OA\Property(property="email", type="array", @OA\Items(example="The email field is required.")),
     *                  @OA\Property(property="password", type="array", @OA\Items(example="The password field is required.")),
     *                  @OA\Property(property="gender", type="array", @OA\Items(example="The gender field is required.")),
     *              )
     *          )
     *      )
     * )
     */
    public function register(CustomerRegister $request)
    {
        $data = $request->validated();
        $customer = Customer::create($data);

        return response()->json([
            'message' => __('customers.customer_registered_successfully'),
            'customer' => $customer,
        ], 201);
    }

    // logout
    /**
     * @OA\Post(
     *      path="/api/customers/auth/logout",
     *      tags={"Customers Authentication"},
     *      summary="Customer logout",
     *      description="Customer logout",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('customers.customer_logged_out_successfully'),
        ], 200);
    }

    // update customer
    /**
     * @OA\Put(
     *      path="/api/customers/auth/update",
     *      tags={"Customers Authentication"},
     *      summary="Customer update",
     *      description="Customer update",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      format="string",
     *                      description="Name",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      format="string",
     *                      description="Password",
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      format="string",
     *                      description="Password confirmation",
     *                  ),
     *                  @OA\Property(
     *                      property="gender",
     *                      type="string",
     *                      format="string",
     *                      description="Gender",
     *                  ),
     *                  @OA\Property(
     *                      property="avatar",
     *                      type="file",
     *                      format="binary",
     *                      description="Avatar file",
     *                  )
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     * )
     */
    public function update(CustomerUpdate $request)
    {
        $customer = Customer::find($request->user()->id);
        if ($customer) {
            $customer->name = $request->name ?? $customer->name;
            $customer->password = Hash::make($request->password) ?? $customer->password;
            $customer->gender = $request->gender ?? $customer->gender;
            if ($request->hasFile('avatar')) {
                $customer->avatar = $request->file('avatar')->store('avatars');
            }
            $customer->save();

            return response()->json([
                'message' => __('customers.customer_updated_successfully'),
                // 'customer' => $customer,
            ], 200);
        }

        return response()->json([
            'message' => __('customers.customer_not_found'),
        ], 404);
    }

    // social login
    /**
     * @OA\Post(
     *      path="/api/customers/auth/social-login",
     *      tags={"Customers Authentication"},
     *      summary="Customer social login",
     *      description="Customer social login",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function socialLogin(Request $request)
    {
        //
    }

    // social register
    /**
     * @OA\Post(
     *      path="/api/customers/auth/social-register",
     *      tags={"Customers Authentication"},
     *      summary="Customer social register",
     *      description="Customer social register",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function socialRegister(Request $request)
    {
        //
    }

    // forgot password
    /**
     * @OA\Post(
     *      path="/api/customers/auth/forgot-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer forgot password",
     *      description="Customer forgot password",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function forgotPassword(Request $request)
    {
        //
    }

    // reset password
    /**
     * @OA\Post(
     *      path="/api/customers/auth/reset-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer reset password",
     *      description="Customer reset password",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {
        //
    }

    // verify code
    /**
     * @OA\Post(
     *      path="/api/customers/auth/verify-code",
     *      tags={"Customers Authentication"},
     *      summary="Customer verify code",
     *      description="Customer verify code",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function verifyCode(Request $request)
    {
        //
    }
}

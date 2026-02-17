<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateCodeRequest;
use App\Http\Requests\ActivateAccountRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPasswordOtpNotification;
use App\Notifications\ActivateAccountOtpNotification;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
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
    public function register(CustomerRegisterRequest $request)
    {
        $data = $request->validated();
        // return "hello";
        $otp = random_int(100000, 999999);
        $data['verification_code'] = $otp;
        $data['verification_code_expires_at'] = now()->addMinutes(10);

        $customer = Customer::create($data);

        $customer->notify(new ActivateAccountOtpNotification($otp));

        return response()->json([
            'message' => 'Verification code sent',
            'customer' => $customer,
        ], 201);
    }
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
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
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
    public function login(CustomerLoginRequest $request)
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
    public function update(CustomerUpdateRequest $request)
    {
        $customer = Customer::find($request->user()->id);
        if ($customer) {
            $customer->name = $request->name ?? $customer->name;
            $customer->password = Hash::make($request->password) ?? $customer->password;
            $customer->gender = $request->gender ?? $customer->gender;
            if ($request->hasFile('avatar')) {
                $file_name = "customers/avatars/" . $customer->id . "/" . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
                Storage::disk('public')->putFileAs( "", $request->file('avatar'), $file_name);
                $customer->avatar = $file_name;
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


    // forgot password
    /**
     * @OA\Post(
     *      path="/api/customers/auth/forgot-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer forgot password",
     *      description="Customer forgot password",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
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
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return response()->json([
                'message' => __('customers.customer_not_found'),
                'errors' => [
                    'email' => [__('customers.customer_not_found')],
                ],
            ], 404);
        }

        // إنشاء كود 6 أرقام
        $otp = random_int(100000, 999999);

        // نحذف أي توكن قديم
        Password::broker('customers')->deleteToken($customer);

        // نحن نريد OTP -> سنخزنه يدويًا

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        $customer->notify(new ResetPasswordOtpNotification($otp));

        return response()->json([
            'message' => 'Verification code sent'
        ]);
    }

    // reset password
    /**
     * @OA\Post(
     *      path="/api/customers/auth/reset-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer reset password",
     *      description="Customer reset password",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="token", type="string", example="123456"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
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
    public function resetPassword(ResetPasswordRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return response()->json([
                'message' => __('customers.customer_not_found'),
                'errors' => [
                    'email' => [__('customers.customer_not_found')],
                ],
            ], 404);
        }

        $record = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

        if (!$record) {
            return response()->json([
                'message' => __('customers.invalid_token'),
                'errors' => [
                    'token' => [__('customers.invalid_token')],
                ],
            ], 400);
        }

        // تحقق من انتهاء الصلاحية (60 دقيقة مثلاً)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return response()->json([
                'message' => __('customers.token_expired'),
                'errors' => [
                    'token' => [__('customers.token_expired')],
                ],
            ], 400);
        }

        // تحقق من الكود
        if (!Hash::check($request->token, $record->token)) {
            return response()->json([
                'message' => __('customers.invalid_token'),
                'errors' => [
                    'token' => [__('customers.invalid_token')],
                ],
            ], 400);
        }

        // تحديث كلمة المرور
        $customer = Customer::where('email', $request->email)->first();

        $customer->password = Hash::make($request->password);
        $customer->save();

        // حذف الكود بعد الاستخدام
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }

    // activate account
    /**
     * @OA\Post(
     *      path="/api/customers/auth/activate-account",
     *      tags={"Customers Authentication"},
     *      summary="Customer activate account",
     *      description="Customer activate account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="code", type="string", example="123456"),
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
    public function activateAccount(ActivateAccountRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return response()->json([
                'message' => __('customers.customer_not_found'),
                'errors' => [
                    'email' => [__('customers.customer_not_found')],
                ],
            ], 404);
        }

        if ($customer->verification_code !== $request->code) {
            return response()->json([
                'message' => __('customers.invalid_code'),
                'errors' => [
                    'code' => [__('customers.invalid_code')],
                ],
            ], 400);
        }

        if ($customer->verification_code_expires_at < now()) {
            return response()->json([
                'message' => __('customers.code_expired'),
                'errors' => [
                    'code' => [__('customers.code_expired')],
                ],
            ], 400);
        }

        $customer->email_verified_at = now();
        $customer->verification_code = null;
        $customer->verification_code_expires_at = null;
        $customer->save();

        return response()->json([
            'message' => 'Account activated successfully',
        ], 200);
    }
}

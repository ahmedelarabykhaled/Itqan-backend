<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateAccountRequest;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use App\Notifications\ActivateAccountOtpNotification;
use App\Notifications\ResetPasswordOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // register
    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/register",
     *      tags={"Customers Authentication"},
     *      summary="Customer register",
     *      description="Customer register",
     *
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
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer registered successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="verification_code", type="integer", example=123456),
     *                  @OA\Property(property="verification_code_expires_at", type="string", format="date-time", example="2026-02-20T10:00:00.000000Z"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T09:50:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T09:50:00.000000Z")
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The email field is required."))
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

        return ApiResponse::success(
            message: 'Customer registered successfully',
            data: $customer,
            status: 200
        );
    }

    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/login",
     *      tags={"Customers Authentication"},
     *      summary="Customer login",
     *      description="Customer login",
     *
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
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer logged in successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *                  @OA\Property(property="avatar", type="string", nullable=true, example="customers/avatars/1/avatar.jpg"),
     *                  @OA\Property(property="token", type="string", example="1|xYzAbCdEf123"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T09:50:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T09:50:00.000000Z")
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Customer not verified",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=401),
     *              @OA\Property(property="message", type="string", example="Customer is not verified"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Customer not found or invalid credentials",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="Customer not found"),
     *              @OA\Property(property="data", type="null", example=null),
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The password field is required."))
     *          )
     *      )
     * )
     */
    public function login(CustomerLoginRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return ApiResponse::error(
                message: __('customers.customer_not_found'),
                status: 404
            );
        }

        if (! $customer->email_verified_at) {
            return ApiResponse::error(
                message: __('customers.customer_not_verified'),
                status: 401
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

    // logout
    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/logout",
     *      tags={"Customers Authentication"},
     *      summary="Customer logout",
     *      description="Customer logout",
     *      security={{"sanctum":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Customer logged out successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer logged out successfully"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(
            message: __('customers.customer_logged_out_successfully'),
            status: 200
        );
    }

    // update customer
    /**
     * @OA\Put(
     *      path="/api/v1/customers/auth/update",
     *      tags={"Customers Authentication"},
     *      summary="Customer update",
     *      description="Customer update",
     *      security={{"sanctum":{}}},
     *
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
     *          description="Customer updated successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Customer updated successfully"),
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
     *          response=404,
     *          description="Customer not found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="Customer not found"),
     *              @OA\Property(property="data", type="null", example=null),
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The password confirmation does not match."))
     *          )
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
                $file_name = 'customers/avatars/'.$customer->id.'/'.time().'.'.$request->file('avatar')->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('', $request->file('avatar'), $file_name);
                $customer->avatar = $file_name;
            }
            $customer->save();

            return ApiResponse::success(
                message: __('customers.customer_updated_successfully'),
                data: $customer,
                status: 200
            );
        }

        return ApiResponse::error(
            message: __('customers.customer_not_found'),
            status: 404
        );
    }

    // forgot password
    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/forgot-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer forgot password",
     *      description="Customer forgot password",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Verification code sent",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Verification code sent"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com")
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The email field is required."))
     *          )
     *      )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return ApiResponse::error(
                message: __('customers.customer_not_found'),
                errors: [
                    __('customers.customer_not_found'),
                ],
                status: 404
            );
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
                'created_at' => now(),
            ]
        );

        $customer->notify(new ResetPasswordOtpNotification($otp));

        return ApiResponse::success(
            message: 'Verification code sent',
            data: [
                'email' => $customer->email,
            ],
            status: 200
        );
    }

    // reset password
    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/reset-password",
     *      tags={"Customers Authentication"},
     *      summary="Customer reset password",
     *      description="Customer reset password",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="token", type="string", example="123456"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Password reset successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Password reset successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Invalid or expired token",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=400),
     *              @OA\Property(property="message", type="string", example="Invalid token"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Invalid token"))
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The token field is required."))
     *          )
     *      )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return ApiResponse::error(
                message: __('customers.customer_not_found'),
                errors: [
                    __('customers.customer_not_found'),
                ],
                status: 404
            );
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return ApiResponse::error(
                message: __('customers.invalid_token'),
                errors: [
                    __('customers.invalid_token'),
                ],
                status: 400
            );
        }

        // تحقق من انتهاء الصلاحية (60 دقيقة مثلاً)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return ApiResponse::error(
                message: __('customers.token_expired'),
                errors: [
                    __('customers.token_expired'),
                ],
                status: 400
            );
        }

        // تحقق من الكود
        if (! Hash::check($request->token, $record->token)) {
            return ApiResponse::error(
                message: __('customers.invalid_token'),
                errors: [
                    __('customers.invalid_token'),
                ],
                status: 400
            );
        }

        // تحديث كلمة المرور
        $customer = Customer::where('email', $request->email)->first();

        $customer->password = Hash::make($request->password);
        $customer->save();

        // حذف الكود بعد الاستخدام
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return ApiResponse::success(
            message: 'Password reset successfully',
            data: $customer,
            status: 200
        );
    }

    // activate account
    /**
     * @OA\Post(
     *      path="/api/v1/customers/auth/activate-account",
     *      tags={"Customers Authentication"},
     *      summary="Customer activate account",
     *      description="Customer activate account",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="code", type="string", example="123456"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Account activated successfully",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Account activated successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                  @OA\Property(property="email_verified_at", type="string", format="date-time", example="2026-02-20T10:00:00.000000Z"),
     *                  @OA\Property(property="verification_code", type="null", example=null),
     *                  @OA\Property(property="verification_code_expires_at", type="null", example=null)
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Invalid or expired activation code",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=400),
     *              @OA\Property(property="message", type="string", example="Invalid code"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="Invalid code"))
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
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string", example="The code field is required."))
     *          )
     *      )
     * )
     */
    public function activateAccount(ActivateAccountRequest $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (! $customer) {
            return ApiResponse::error(
                message: __('customers.customer_not_found'),
                errors: [
                    __('customers.customer_not_found'),
                ],
                status: 404
            );
        }

        if ($customer->verification_code !== $request->code) {
            return ApiResponse::error(
                message: __('customers.invalid_code'),
                errors: [
                    __('customers.invalid_code'),
                ],
                status: 400
            );
        }

        if ($customer->verification_code_expires_at < now()) {
            return ApiResponse::error(
                message: __('customers.code_expired'),
                errors: [
                    __('customers.code_expired'),
                ],
                status: 400
            );
        }

        $customer->email_verified_at = now();
        $customer->verification_code = null;
        $customer->verification_code_expires_at = null;
        $customer->save();

        return ApiResponse::success(
            message: 'Account activated successfully',
            data: $customer,
            status: 200
        );
    }
}

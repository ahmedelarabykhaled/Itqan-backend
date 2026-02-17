<?php

namespace App\Http\Services;

class AuthService
{
    public function register(CustomerRegister $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'avatar' => $request->avatar,
        ]);

        return $customer;
    }

    public function login(CustomerLogin $request)
    {
        $customer = Customer::where('email', $request->email)->verified()->first();
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => __('customers.customer_not_found'),
            ], 404);
        }

        if (!$customer->email_verified_at) {
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
}
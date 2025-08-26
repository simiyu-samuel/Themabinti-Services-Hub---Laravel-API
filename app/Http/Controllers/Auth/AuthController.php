<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Exception;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $startTime = microtime(true);
        $requestId = uniqid('reg_');
        $isSellerRegistration = ($request->user_type === 'seller');
        $sellerPackageChoice = $request->seller_package; // Will be null if user_type is customer
        
        try {
            // Log registration attempt
            Log::info('Registration attempt started', [
                'request_id' => $requestId,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            // Debug: Log validated data
            Log::debug('Registration validated data', [
                'request_id' => $requestId,
                'data' => $request->safe()->except(['password']), // Exclude password from logs
                'has_password' => !empty($request->password)
            ]);

            // Start database transaction for debugging
            DB::beginTransaction();
            Log::debug('Database transaction started', ['request_id' => $requestId]);

            // Create user with debugging
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'user_type' => 'customer', // <<< ALWAYS REGISTER AS CUSTOMER INITIALLY
                'seller_package' => null,   // <<< NULL until payment
                'package_expiry_date' => null, // <<< NULL until payment
                'pending_seller_package' => $isSellerRegistration ? $sellerPackageChoice : null, // <<< STORE PENDING CHOICE
                'is_active' => true,
            ];

            Log::debug('Creating user with data', [
                'request_id' => $requestId,
                'user_data' => collect($userData)->except(['password'])->toArray()
            ]);

            $user = User::create($userData);

            Log::info('User created successfully', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type
            ]);

            // Fire registered event
            Log::debug('Firing Registered event', [
                'request_id' => $requestId,
                'user_id' => $user->id
            ]);
            
            event(new Registered($user));

            // Create token
            Log::debug('Creating authentication token', [
                'request_id' => $requestId,
                'user_id' => $user->id
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Authentication token created', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'token_name' => 'auth_token'
            ]);

            // Commit transaction
            DB::commit();
            Log::debug('Database transaction committed', ['request_id' => $requestId]);

            $responseData = [
                'message' => 'Registration successful. Please verify your email.',
                'user' => $user->only(['id', 'name', 'email', 'user_type', 'phone_number', 'email_verified_at', 'pending_seller_package']),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'needs_seller_payment' => $isSellerRegistration, // <<< FLAG FOR FRONTEND
            ];

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Registration completed successfully', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'processing_time_ms' => $processingTime,
                'response_code' => 201
            ]);

            return response()->json($responseData, 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::warning('Registration validation failed', [
                'request_id' => $requestId,
                'email' => $request->email ?? 'unknown',
                'errors' => $e->errors(),
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);
            
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Registration failed with exception', [
                'request_id' => $requestId,
                'email' => $request->email ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $startTime = microtime(true);
        $requestId = uniqid('login_');
        
        try {
            // Log login attempt
            Log::info('Login attempt started', [
                'request_id' => $requestId,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            // Debug: Log request data (without password)
            Log::debug('Login request data', [
                'request_id' => $requestId,
                'email' => $request->email,
                'has_password' => !empty($request->password)
            ]);

            // Find user
            Log::debug('Searching for user', [
                'request_id' => $requestId,
                'email' => $request->email
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::warning('Login failed - user not found', [
                    'request_id' => $requestId,
                    'email' => $request->email,
                    'ip_address' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }

            Log::debug('User found', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at
            ]);

            // Check password
            Log::debug('Checking password', [
                'request_id' => $requestId,
                'user_id' => $user->id
            ]);

            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Login failed - invalid password', [
                    'request_id' => $requestId,
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'ip_address' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }

            // Check if user is active
            if (!$user->is_active) {
                Log::warning('Login failed - inactive account', [
                    'request_id' => $requestId,
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'Your account is inactive. Please contact support.'
                ], 403);
            }

            // Revoke existing tokens
            Log::debug('Revoking existing tokens', [
                'request_id' => $requestId,
                'user_id' => $user->id
            ]);

            $existingTokensCount = $user->tokens()->count();
            $user->tokens()->delete();

            Log::debug('Existing tokens revoked', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'tokens_revoked' => $existingTokensCount
            ]);

            // Create new token
            Log::debug('Creating new authentication token', [
                'request_id' => $requestId,
                'user_id' => $user->id
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $responseData = [
                'message' => 'Login successful.',
                'user' => $user->only(['id', 'name', 'email', 'user_type', 'phone_number', 'email_verified_at']),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Login completed successfully', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'processing_time_ms' => $processingTime,
                'response_code' => 200
            ]);

            return response()->json($responseData);

        } catch (ValidationException $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::warning('Login validation failed', [
                'request_id' => $requestId,
                'email' => $request->email ?? 'unknown',
                'errors' => $e->errors(),
                'processing_time_ms' => $processingTime
            ]);
            
            throw $e;

        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('Login failed with exception', [
                'request_id' => $requestId,
                'email' => $request->email ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            return response()->json([
                'message' => 'Login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $startTime = microtime(true);
        $requestId = uniqid('logout_');
        
        try {
            $user = $request->user();
            
            Log::info('Logout attempt started', [
                'request_id' => $requestId,
                'user_id' => $user ? $user->id : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            if (!$user) {
                Log::warning('Logout failed - no authenticated user', [
                    'request_id' => $requestId,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'Not authenticated.'
                ], 401);
            }

            Log::debug('User authenticated for logout', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Get current token info before deletion
            $currentToken = $request->user()->currentAccessToken();
            $tokenId = $currentToken ? $currentToken->id : null;

            Log::debug('Deleting current access token', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'token_id' => $tokenId
            ]);

            // Delete the current token
            $currentToken->delete();

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Logout completed successfully', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'token_id' => $tokenId,
                'processing_time_ms' => $processingTime,
                'response_code' => 200
            ]);

            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);

        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            $user = $request->user();
            
            Log::error('Logout failed with exception', [
                'request_id' => $requestId,
                'user_id' => $user ? $user->id : null,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            return response()->json([
                'message' => 'Logout failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
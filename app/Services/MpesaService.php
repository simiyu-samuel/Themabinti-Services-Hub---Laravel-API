<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // For caching access token
use Illuminate\Support\Facades\Config;

class MpesaService
{
    protected Client $client;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $passkey;
    protected string $shortcode;
    protected string $callbackUrl;
    protected string $env;
    
    protected AdminSettingsService $adminSettingsService;
    /**
     * Constructor to initialize M-Pesa service with configuration.
     * Throws an exception if critical credentials are missing.
     */
    public function __construct(AdminSettingsService $adminSettingsService) // Inject
    {
        $this->adminSettingsService = $adminSettingsService; // Store the service

        // --- FETCH CREDENTIALS FROM ADMIN SETTINGS SERVICE ---
        $this->consumerKey = env('MPESA_CONSUMER_KEY') ?? $this->adminSettingsService->get('mpesa_consumer_key');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET') ?? $this->adminSettingsService->get('mpesa_consumer_secret');
        $this->passkey = env('MPESA_PASSKEY') ?? $this->adminSettingsService->get('mpesa_passkey');
        $this->shortcode = env('MPESA_SHORTCODE');
        $this->tillnumber = env('MPESA_TILLNUMBER');

        // Callback URL and Environment are still best from .env or config/mpesa.php directly
        // because they define the backend environment's own identity.
        $this->callbackUrl = Config::get('mpesa.callback_url');
        $this->env = Config::get('mpesa.environment');

        // Add a check for missing credentials and throw an exception if critical ones are missing
        if (empty($this->consumerKey) || empty($this->consumerSecret) || empty($this->passkey) || empty($this->shortcode) || empty($this->callbackUrl)) {
            Log::error('M-Pesa credentials or callback URL are not fully configured. Check admin settings and/or .env/config/mpesa.php.');
            throw new \Exception('M-Pesa service is not fully configured.');
        }

        $baseUri = $this->env === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/'
            : 'https://api.safaricom.co.ke/';

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => ['Content-Type' => 'application/json']
        ]);
    }
    /**
     * Generate M-Pesa API Access Token.
     * Caches the token for an hour.
     */
    public function generateAccessToken(): ?string
    {
        $cacheKey = 'mpesa_access_token';

        // Try to retrieve from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->request('GET', 'oauth/v1/generate?grant_type=client_credentials', [
                'auth' => [$this->consumerKey, $this->consumerSecret]
            ]);
            $data = json_decode($response->getBody()->getContents(), true); // Read body as string
            $accessToken = $data['access_token'] ?? null;

            if ($accessToken) {
                // Cache token for 1 hour (M-Pesa tokens typically expire after 1 hour)
                Cache::put($cacheKey, $accessToken, now()->addMinutes(59));
            }
            return $accessToken;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            Log::error("M-Pesa Access Token Client Error ({$statusCode}): " . $responseBody);
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            Log::error("M-Pesa Access Token Server Error ({$statusCode}): " . $responseBody);
        } catch (\Exception $e) {
            Log::error('M-Pesa Access Token General Error: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Initiate an M-Pesa STK Push transaction.
     *
     * @param float  $amount
     * @param string $phone           Customer's phone number (2547XXXXXXXX)
     * @param string $accountReference Your system's unique reference for this transaction (e.g., invoice ID)
     * @param string $transactionDesc Description for the transaction (shown to customer)
     * @return array                 Success/failure status and M-Pesa response data
     */
    public function stkPush(float $amount, string $phone, string $accountReference, string $transactionDesc): array
    {
        $accessToken = $this->generateAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to obtain M-Pesa access token.'];
        }
        
        // dd($accessToken);
        // Generate Timestamp and Password for STK Push
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        // dd($this->callbackUrl);

        // dd($this->tillnumber);
        try {
            $response = $this->client->request('POST', 'mpesa/stkpush/v1/processrequest', [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'json' => [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerBuyGoodsOnline', // For Pay Bill. Use 'CustomerBuyGoodsOnline' for Till Number.
                    'Amount' => round($amount), // Amount must be an integer (no decimals for M-Pesa)
                    'PartyA' => 254796590401,
                    'PartyB' => $this->tillnumber,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $this->callbackUrl,
                    'AccountReference' => $accountReference,
                    'TransactionDesc' => $transactionDesc,
                ]
            ]);
            
//             Payload: {
//   "BusinessShortCode": "5657484",
//   "Password":"NTY1NzQ4NDJkZWU0Y2M0OTNhOWUzMDU0M2NmMTQ4YmU4Y2YwNDhiZDU0MTgwNDA1ZDc1N2MxZTg2YmU0NzMwNThlNzljZTIyMDI1MDcyMTE4MTgyNA==",
//   "Timestamp": "20250721181824",
//   "TransactionType": "CustomerBuyGoodsOnline",
//   "Amount": 1000,
//   "PartyA": "254796590401",
//   "PartyB": "5657484",
//   "PhoneNumber": "254796590401",
//   "CallBackURL": "https://themabinti-main-d4az.onrender.com/api/mpesa/callback",
//   "AccountReference": "PKG-basic",
//   "TransactionDesc": "Payment for Basic Package package"
// }

            $data = json_decode($response->getBody()->getContents(), true);
            Log::info('STK Push Request Sent:', $data);

            if (isset($data['ResponseCode']) && $data['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'CheckoutRequestID' => $data['CheckoutRequestID'],
                    'CustomerMessage' => $data['CustomerMessage'] ?? 'STK push initiated successfully. Please enter your M-Pesa PIN.'
                ];
            } else {
                $errorMessage = $data['ResponseDescription'] ?? $data['errorMessage'] ?? 'Unknown error from M-Pesa.';
                return ['success' => false, 'message' => $errorMessage, 'data' => $data];
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            Log::error("STK Push Client Error ({$statusCode}): " . $responseBody);
            return ['success' => false, 'message' => 'M-Pesa STK Push failed due to client error.', 'data' => json_decode($responseBody, true)];
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            Log::error("STK Push Server Error ({$statusCode}): " . $responseBody);
            return ['success' => false, 'message' => 'M-Pesa STK Push failed due to server error.', 'data' => json_decode($responseBody, true)];
        } catch (\Exception $e) {
            Log::error('STK Push General Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'STK Push failed due to an unexpected error.'];
        }
    }

    /**
     * Query the status of an STK Push transaction.
     * Useful if the callback is delayed or missed.
     *
     * @param string $checkoutRequestId
     * @return array
     */
    public function queryStkStatus(string $checkoutRequestId): array
    {
        $accessToken = $this->generateAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to obtain M-Pesa access token.'];
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        

        try {
            $response = $this->client->request('POST', 'mpesa/stkpushquery/v1/query', [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'json' => [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            Log::info('STK Push Query Response:', $data);

            if (isset($data['ResponseCode']) && $data['ResponseCode'] == '0') {
                // Transaction is still pending or completed/failed
                return ['success' => true, 'data' => $data];
            } else {
                return ['success' => false, 'message' => $data['ResponseDescription'] ?? $data['errorMessage'] ?? 'Unknown status query error', 'data' => $data];
            }
        } catch (ClientException | ServerException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            Log::error('STK Query Error: ' . $e->getMessage() . ' Response: ' . $responseBody);
            return ['success' => false, 'message' => 'Failed to query M-Pesa transaction status.', 'data' => json_decode($responseBody, true)];
        } catch (\Exception $e) {
            Log::error('STK Query General Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An unexpected error occurred during status query.'];
        }
    }
}
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait DetectsCountry
{
    /**
     * Detect country code from IP address.
     *
     * @param string $ip
     * @return string|null
     */
    protected function detectCountry(string $ip): ?string
    {
        try {
            // 1. Check for Cloudflare header (Best for production if using CF)
            if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
                $country = $_SERVER['HTTP_CF_IPCOUNTRY'];
                Log::info("Detected country code: {$country} via Cloudflare header for IP: {$ip}");
                return $country;
            }

            Log::info("Attempting country detection for IP: {$ip}");

            // In development, handle local IP addresses
            if (($ip === '127.0.0.1' || $ip === '::1')) {
                if (app()->environment(['local', 'testing'])) {
                    Log::info("Local IP detected in dev environment, using fallback 8.8.8.8 (USA)");
                    $ip = '8.8.8.8';
                } else {
                    Log::warning("Local IP detected in non-dev environment: {$ip}. Skipping detection.");
                    return null;
                }
            }

            // 2. Use ipapi.co (Supports HTTPS for free)
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['country_code'])) {
                    $countryCode = $data['country_code'];
                    Log::info("Detected country code: {$countryCode} via ipapi.co for IP: {$ip}");
                    return $countryCode;
                }
                Log::warning("ipapi.co returned no country code: " . json_encode($data));
            } else {
                Log::warning("ipapi.co request failed with status: " . $response->status());
                
                // 3. Fallback to ip-api.com (HTTP only) if HTTPS failed/blocked
                Log::info("Falling back to ip-api.com (HTTP)...");
                $fallbackResponse = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($fallbackResponse->successful()) {
                    $fbData = $fallbackResponse->json();
                    if (isset($fbData['status']) && $fbData['status'] === 'success') {
                        $countryCode = $fbData['countryCode'] ?? null;
                        Log::info("Detected country code: {$countryCode} via ip-api.com (fallback) for IP: {$ip}");
                        return $countryCode;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Country detection exception: ' . $e->getMessage());
        }

        return null;
    }
}

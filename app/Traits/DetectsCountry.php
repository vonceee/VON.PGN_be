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
            // In development, handle local IP addresses
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $ip = '8.8.8.8'; // Fallback for local testing
            }

            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    return $data['countryCode'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Country detection failed: ' . $e->getMessage());
        }

        return null;
    }
}

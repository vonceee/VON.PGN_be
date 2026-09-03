<?php

namespace App\Traits;

trait DetectsCountry
{
    /**
     * Detect country code from request headers (Cloudflare CF-IPCountry) with zero network latency.
     * External HTTP geolocation APIs have been removed to eliminate request blocking and latency spikes.
     *
     * @param string|null $ip
     * @return string|null
     */
    protected function detectCountry(?string $ip = null): ?string
    {
        // 0ms header lookup (populated by Cloudflare proxy on production)
        return request()->header('CF-IPCountry') ?? ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? null);
    }
}


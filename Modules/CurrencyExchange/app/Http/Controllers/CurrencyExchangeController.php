<?php

namespace Modules\CurrencyExchange\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyExchangeController extends Controller
{
    private string $bnrUrl = 'https://curs.bnr.ro/nbrfxrates.xml';

    // ─── Views ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $rates          = $this->getRates();
        $currency       = $request->get('currency', 'EUR');
        $date           = $request->get('date', null);
        $mainCurrencies = ['EUR', 'USD', 'GBP', 'CHF', 'JPY', 'CAD', 'AUD'];

        return view('currencyexchange::index', compact('rates', 'currency', 'date', 'mainCurrencies'));
    }

    // ─── API Endpoints ─────────────────────────────────────────────────────────

    public function rates(Request $request)
    {
        return response()->json($this->getRates());
    }

    public function historical(Request $request)
    {
        $date = $request->get('date');

        // Sanitize: only accept YYYY-MM-DD
        if ($date && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
        }

        Log::info('[CurrencyExchange] Historical request', ['date' => $date]);

        return response()->json($this->getRatesForDate($date));
    }

    // ─── Core Rate Fetching ────────────────────────────────────────────────────

    /**
     * Get today's rates, cached for 6 hours.
     */
    private function getRates(): array
    {
        return Cache::remember('bnr_rates_today', 60 * 60 * 6, function () {
            return $this->fetchFromBNR($this->bnrUrl);
        });
    }

    /**
     * Get rates for a specific date, with fallback to today's cache.
     */
    private function getRatesForDate(?string $date): array
    {
        if (! $date) {
            return $this->getRates();
        }

        // If the requested date matches what's already cached as today, return it
        $todayRates = Cache::get('bnr_rates_today', []);
        if (! empty($todayRates)) {
            $cachedDate = $todayRates[array_key_first($todayRates)]['date'] ?? null;
            if ($cachedDate === $date) {
                return $todayRates;
            }
        }

        return Cache::remember("bnr_rates_{$date}", 60 * 60 * 24, function () use ($date) {
            return $this->fetchHistoricalRatesForDate($date);
        });
    }

    /**
     * Fetch historical rates for a specific date from the BNR yearly XML.
     * Falls back up to 7 previous working days if the exact date has no data.
     */
    private function fetchHistoricalRatesForDate(string $date): array
    {
        $year        = substr($date, 0, 4);
        $yearUrl     = "https://curs.bnr.ro/files/xml/years/nbrfxrates{$year}.xml";
        $currentDate = new \DateTime($date);

        Log::debug('[CurrencyExchange] Fetching yearly XML', ['year' => $year, 'url' => $yearUrl]);

        // Cache the full year XML (shorter TTL for current year since it keeps updating)
        $ttl    = ($year === date('Y')) ? 60 * 60 * 2 : 60 * 60 * 24 * 30;
        $yearXml = Cache::remember("bnr_year_xml_{$year}", $ttl, function () use ($yearUrl) {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept'     => 'text/xml,application/xml,*/*',
                ])
                ->get($yearUrl);

            if (! $response->successful()) {
                Log::warning('[CurrencyExchange] Failed to fetch yearly XML', [
                    'status' => $response->status(),
                    'url'    => $yearUrl,
                ]);
                return null;
            }

            return $response->body();
        });

        if (! $yearXml) {
            return [];
        }

        // Try the requested date, then walk back through working days
        for ($i = 0; $i < 7; $i++) {
            $tryDate = $currentDate->format('Y-m-d');
            $rates   = $this->parseRatesForDate($yearXml, $tryDate);

            if (! empty($rates)) {
                Log::info('[CurrencyExchange] Found rates', [
                    'requested' => $date,
                    'found'     => $tryDate,
                ]);
                return $rates;
            }

            // Step back one calendar day, then skip weekends
            $currentDate->modify('-1 day');
            while ($currentDate->format('N') >= 6) {
                $currentDate->modify('-1 day');
            }
        }

        Log::warning('[CurrencyExchange] No rates found after fallback', ['date' => $date]);

        return [];
    }

    /**
     * Parse rates for a specific date from a full-year XML string.
     */
    private function parseRatesForDate(string $xmlBody, string $date): array
    {
        try {
            $xml = simplexml_load_string($xmlBody);
            if (! $xml) {
                return [];
            }

            foreach ($xml->Body->Cube as $cube) {
                $cubeDate = (string) $cube['date'];
                if ($cubeDate !== $date) {
                    continue;
                }

                return $this->parseCubeRates($cube, $cubeDate);
            }
        } catch (\Exception $e) {
            Log::error('[CurrencyExchange] Parse error', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Fetch today's rates from a BNR XML feed.
     * Uses the last <Cube> node in the document to be safe with multi-cube feeds.
     */
    private function fetchFromBNR(string $url): array
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept'     => 'text/xml,application/xml,*/*',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('[CurrencyExchange] BNR request failed', [
                    'status' => $response->status(),
                    'url'    => $url,
                ]);
                return [];
            }

            $xml = simplexml_load_string($response->body());
            if (! $xml || ! isset($xml->Body->Cube)) {
                return [];
            }

            // Use the last <Cube> to handle feeds that may contain multiple cubes
            $cubes    = $xml->Body->Cube;
            $lastCube = $cubes[count($cubes) - 1];
            $date     = (string) $lastCube['date'];

            return $this->parseCubeRates($lastCube, $date);

        } catch (\Exception $e) {
            Log::error('[CurrencyExchange] Error fetching rates', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Parse a single <Cube> node into a rates array.
     */
    private function parseCubeRates(\SimpleXMLElement $cube, string $date): array
    {
        $rates = [];

        foreach ($cube->Rate as $rate) {
            $currency   = (string) $rate['currency'];
            $multiplier = (int) ($rate['multiplier'] ?? 1);
            $value      = (float) $rate;

            $rates[$currency] = [
                'currency'   => $currency,
                'rate'       => $multiplier > 1 ? round($value / $multiplier, 4) : $value,
                'multiplier' => $multiplier,
                'raw'        => $value,
                'date'       => $date,
            ];
        }

        return $rates;
    }
}

<?php

namespace Modules\CurrencyExchange\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyExchangeController extends Controller
{
    private string $bnrUrl        = 'https://curs.bnr.ro/nbrfxrates.xml';
    private string $bnr10DaysUrl  = 'https://curs.bnr.ro/nbrfxrates10days.xml';

    public function index(Request $request)
    {
        $rates          = $this->getRates();
        $currency       = $request->get('currency', 'EUR');
        $date           = $request->get('date', null);
        $mainCurrencies = ['EUR', 'USD', 'GBP', 'CHF', 'JPY', 'CAD', 'AUD'];

        return view('currencyexchange::index', compact('rates', 'currency', 'date', 'mainCurrencies'));
    }

    public function rates(Request $request)
    {
        $rates = $this->getRates();
        return response()->json($rates);
    }

    public function historical(Request $request)
    {
        $date  = $request->get('date');
        \Log::info("Historical request", ['date' => $date]);
        $rates = $this->getRatesForDate($date);
        return response()->json($rates);
    }

    private function getRates(): array
    {
        return Cache::remember('bnr_rates_today', 60 * 60 * 6, function () {
            return $this->fetchFromBNR($this->bnrUrl);
        });
    }

    private function getRatesForDate(?string $date): array
    {
        if (!$date) {
            return $this->getRates();
        }

        // Check if requested date matches today's cache
        $todayRates = Cache::get('bnr_rates_today', []);
        if (!empty($todayRates)) {
            $cachedDate = $todayRates[array_key_first($todayRates)]['date'] ?? null;
            if ($cachedDate === $date) {
                return $todayRates;
            }
        }

        return Cache::remember("bnr_rates_{$date}", 60 * 60 * 24, function () use ($date) {
            return $this->fetchHistoricalRatesForDate($date);
        });
    }

    private function fetchHistoricalRatesForDate(string $date): array
    {
        $year        = substr($date, 0, 4);
        $yearUrl     = "https://curs.bnr.ro/files/xml/years/nbrfxrates{$year}.xml";
        $currentDate = new \DateTime($date);

        \Log::debug('Fetching yearly XML', ['year' => $year, 'url' => $yearUrl]);

        // Cache the entire year XML for 24 hours
        $yearXml = Cache::remember("bnr_year_xml_{$year}", 60 * 60 * 24, function () use ($yearUrl) {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept'     => 'text/xml,application/xml,*/*',
                ])
                ->get($yearUrl);

            if (!$response->successful()) {
                \Log::warning('Failed to fetch yearly XML', ['status' => $response->status()]);
                return null;
            }

            return $response->body();
        });

        if (!$yearXml) {
            return [];
        }

        // Try requested date and fallback to previous working days
        for ($i = 0; $i < 7; $i++) {
            $tryDate = $currentDate->format('Y-m-d');
            $rates   = $this->parseRatesForDate($yearXml, $tryDate);

            if (!empty($rates)) {
                \Log::info('Found rates', ['requested' => $date, 'found' => $tryDate]);
                return $rates;
            }

            $currentDate->modify('-1 day');

            // Skip weekends
            while ($currentDate->format('N') >= 6) {
                $currentDate->modify('-1 day');
            }
        }

        return [];
    }

    private function parseRatesForDate(string $xmlBody, string $date): array
    {
        try {
            $xml = simplexml_load_string($xmlBody);
            if (!$xml) return [];

            foreach ($xml->Body->Cube as $cube) {
                $cubeDate = (string) $cube['date'];
                if ($cubeDate !== $date) continue;

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
                        'date'       => $cubeDate,
                    ];
                }
                return $rates;
            }
        } catch (\Exception $e) {
            \Log::error('Parse error', ['error' => $e->getMessage()]);
        }

        return [];
    }

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

            if (!$response->successful()) {
                \Log::warning('BNR request failed', ['status' => $response->status(), 'url' => $url]);
                return [];
            }

            $xml = simplexml_load_string($response->body());
            if (!$xml) return [];

            if (!isset($xml->Body->Cube->Rate)) return [];

            $rates = [];
            $date  = (string) $xml->Body->Cube['date'];

            foreach ($xml->Body->Cube->Rate as $rate) {
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

        } catch (\Exception $e) {
            \Log::error('Error fetching rates', ['url' => $url, 'error' => $e->getMessage()]);
            return [];
        }
    }
}
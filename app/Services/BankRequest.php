<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankRequest
{
    private array $bank = [];

    public function __construct()
    {
        $this->bank = Cache::remember('cbr_xml_daily', now()->addHours(1), function () {
            $urls = [
                'https://www.cbr.ru/scripts/XML_daily.asp',
                'https://www.cbr-xml-daily.ru/daily_utf8.xml',
            ];

            foreach ($urls as $url) {
                try {
                    $body =  Http::retry(2, 200)
                        ->connectTimeout(5)->timeout(20)
                        ->withUserAgent('Mozilla/5.0')
                        ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                        ->get($url)->throw()->body();
                } catch (\Throwable $e) {
                    $body = '';
                    Log::warning('Http "' . $url . '" '. $e->getMessage());
                }
            }

            $xml = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);

            if ($xml === false) {
                return [];
            }

            return json_decode(json_encode($xml), true) ?: [];
        });
    }

    public function get(): array
    {
        return $this->bank;
    }

    public function getValue(string $code): float
    {
        $valutes = $this->bank['Valute'] ?? [];
        foreach ($valutes as $valute) {
            if (($valute['CharCode'] ?? null) === $code) {
                $value = (string)($valute['Value'] ?? '0');
                $value = str_replace([' ', ','], ['', '.'], $value);

                return (float)$value;
            }
        }
        return 0.0;
    }
}

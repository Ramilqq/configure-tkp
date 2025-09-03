<?php

namespace App\Services;

class BankRequest
{

    public array $bank = [];

    public function __construct()
    {
        $xml = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp");
        $json = json_encode($xml);
        $this->bank = json_decode($json,TRUE);
    }

    public function get() {
        return $this->bank;
    }

    public function getValue($code) {

        foreach($this->get()['Valute'] as $valute){
            if ($valute['CharCode'] == $code) return $valute['Value'];
        }
        return 0.0;
    }

}

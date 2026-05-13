<?php

namespace App\Services\FrService;

use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use Illuminate\Database\Eloquent\Collection;

class FrOptionsAppliedService
{
    public function apply(array $data_filter = [], Collection $options = null, Collection $optionPrices = null): array
    {
        $option_applied = [];

        // формирование базовых опций
        foreach ($options as $option) {
            $option_applied[$option->templateOption->key] = [
                'key' => $option->templateOption->key,
                'value' => $option->value,
                'drawing' => null,
                'airflow' => null,
                'dimension' => null,
                'weight' => null,
                'service' => null,
                'price' => null,
                'rename_title' => null,
                'rename_title_end' => null,
            ];
        }

        // формирование динамических опций
        foreach ($optionPrices as $optionPrice) {
            if (!isset($data_filter[$optionPrice->templateOption->key])) continue;
            if ($data_filter[$optionPrice->templateOption->key] != $optionPrice->value) continue;

            if($optionPrice->drawing && $optionPrice->drawing != '???') {
                $drawing = $optionPrice->drawing;
            } else {
                $drawing = null;
            }

            $option_applied[$optionPrice->templateOption->key] = [
                'key' => $optionPrice->templateOption->key,
                'value' => $optionPrice->value,
                'drawing' => $drawing,
                'airflow' => $optionPrice->airflow,
                'dimension' => $optionPrice->dimension,
                'weight' => $optionPrice->weight,
                'service' => $optionPrice->service,
                'price' => $optionPrice->price,
                'rename_title' => $optionPrice->rename_title,
                'rename_title_end' => $optionPrice->rename_title_end,
            ];
        }

        return $option_applied;
    }
}

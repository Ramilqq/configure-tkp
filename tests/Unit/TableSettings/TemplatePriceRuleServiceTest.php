<?php

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\Template;
use App\Models\TableSettings\TemplatePriceRule;
use App\Services\TableSettings\TemplatePriceRuleService;

it('adds fixed value to price when checkbox rule matches', function () {
    $rule = new TemplatePriceRule([
        'id' => 10,
        'name' => 'IP54',
        'key' => 'ip54',
        'enabled' => true,
        'target_field' => 'price',
        'mode' => 'add',

        'condition_operator' => 'equals',
        'condition_value' => '1',
        'condition_field' => 'checkbox',

        'fixed_value' => 15,

        'generation_name_status' => true,
        'generation_name_text' => 'IP54',
    ]);

    $template = new Template([
        'id' => 1,
        'name' => 'ЧРП',
    ]);
    $template->setRelation('priceRules', collect([$rule]));

    $product = new Product([
        'template_id' => 1,
        'price' => '100',
        'delivery' => '20',
    ]);
    $product->setRelation('template', $template);
    $product->setRelation('productOption', collect());

    $result = app(TemplatePriceRuleService::class)->apply($product, [
        'ip54' => 'on',
    ]);

    expect($result['price'])->toBe(115.0)
        ->and($result['delivery'])->toBe(20.0)
        ->and($result['applied_rules'])->toHaveCount(1)
        ->and($result['applied_rules'][0]['rule_key'])->toBe('ip54')
        ->and($result['applied_rules'][0]['generation_name'])->toBe('IP54')
        ->and($result['applied_rules'][0]['before'])->toBe(100.0)
        ->and($result['applied_rules'][0]['after'])->toBe(115.0);
});

it('replaces delivery using mapped value from driver option range', function () {
    $rule = new TemplatePriceRule([
        'id' => 20,
        'name' => 'Пусконаладка',
        'key' => 'startup',
        'enabled' => true,
        'target_field' => 'delivery',
        'mode' => 'replace',

        'condition_operator' => 'exists',
        'condition_field' => 'checkbox',

        'driver_option_id' => 55,
        'mapping' => [
            ['from' => 0, 'to' => 199, 'condition' => '1', 'text' => '', 'value' => 7.5],
            ['from' => 200, 'to' => 400, 'condition' => '1', 'text' => '', 'value' => 12.5],
        ],
    ]);

    $template = new Template([
        'id' => 1,
        'name' => 'ЧРП',
    ]);
    $template->setRelation('priceRules', collect([$rule]));

    $product = new Product([
        'template_id' => 1,
        'price' => '100',
        'delivery' => '20',
    ]);
    $product->setRelation('template', $template);
    $product->setRelation('productOption', collect([
        new ProductOption([
            'template_option_id' => 55,
            'value' => '160',
        ]),
    ]));

    $result = app(TemplatePriceRuleService::class)->apply($product, [
        'startup' => '1',
    ]);
//dd($result);
    expect($result['price'])->toBe(100.0)
        ->and($result['delivery'])->toBe(7.5)
        ->and($result['applied_rules'])->toHaveCount(1)
        ->and($result['applied_rules'][0]['mapped_or_fixed_value'])->toBe(7.5);
});
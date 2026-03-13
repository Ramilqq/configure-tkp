<?php

use App\Models\TableSettings\TemplateDimensionScheme;
use App\Services\TableSettings\DimensionSchemeResolver;

it('returns only enabled schemes that match product options and applied rules', function () {
    $matching = new TemplateDimensionScheme([
        'id' => 1,
        'template_id' => 1,
        'name' => 'Схема A',
        'enabled' => true,
        'match_mode' => 'all',
        'conditions' => [
            ['option_key' => 'motor_type', 'op' => 'equals', 'value' => 'Синхронный'],
            ['option_key' => 'voltage', 'op' => 'in', 'value' => '6000,10000'],
        ],
        'rule_conditions' => [
            ['rule_key' => 'cooling', 'op' => 'equals', 'value' => 'water'],
        ],
    ]);

    $notMatching = new TemplateDimensionScheme([
        'id' => 2,
        'template_id' => 1,
        'name' => 'Схема B',
        'enabled' => true,
        'match_mode' => 'all',
        'conditions' => [
            ['option_key' => 'motor_type', 'op' => 'equals', 'value' => 'Асинхронный'],
        ],
        'rule_conditions' => [],
    ]);

    $disabled = new TemplateDimensionScheme([
        'id' => 3,
        'template_id' => 1,
        'name' => 'Схема C',
        'enabled' => false,
        'match_mode' => 'all',
        'conditions' => [
            ['option_key' => 'motor_type', 'op' => 'equals', 'value' => 'Синхронный'],
        ],
        'rule_conditions' => [],
    ]);

    $resolver = new DimensionSchemeResolver();

    // Подменяем приватный cache, чтобы не ходить в БД.
    (function (array $cache) {
        $this->cache = $cache;
    })->call($resolver, [
        1 => collect([$matching, $notMatching, $disabled]),
    ]);

    $node = [
        'template_id' => 1,
        'product' => [
            'product_option' => [
                [
                    'value' => 'Синхронный',
                    'get_name' => ['key' => 'motor_type'],
                ],
                [
                    'value' => '6000',
                    'get_name' => ['key' => 'voltage'],
                ],
            ],
            'price_rules_applied' => [
                [
                    'rule_key' => 'cooling',
                    'form_value' => 'water',
                ],
            ],
        ],
    ];

    $result = $resolver->resolveForNode($node);

    expect($result)->toHaveCount(1)
        ->and($result[0]->name)->toBe('Схема A');
});
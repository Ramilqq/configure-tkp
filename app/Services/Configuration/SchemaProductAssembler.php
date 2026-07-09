<?php

namespace App\Services\Configuration;

use App\Enums\TemplateType;
use App\Models\TableSettings\Product;
use App\Services\BankRequest;
use App\Services\FrService\FrOptionsAppliedService;
use App\Services\ReplaceProduct;
use App\Services\TableSettings\TemplatePriceRuleService;

/**
 * Собирает массив «product» для сохранения в saved_schema конфигурации:
 * применение опций, замена наименования/описания, правила цены,
 * курс валюты и дефолтные поля расчёта ТКП.
 */
class SchemaProductAssembler
{
    /** Дефолтные поля расчёта ТКП, добавляемые к продукту в схеме */
    private const TKP_CALC_DEFAULTS = [
        'discount' => 0,
        'text' => '',
        'sel_price_coef' => 1,
        'gen_contract_service' => 0,
        'costs_credit' => 0,
        'risk_reserve' => 0,
        'tzr_sel' => 0,
        'sub_work' => 0,
        'sub_item_price' => 0,
        'tzr_delivery' => 0,
        'biz_trips' => 0,
        'connection' => 0,
    ];

    public function __construct(
        private BankRequest $banks,
        private TemplatePriceRuleService $priceRules,
        private ReplaceProduct $replaceProduct,
        private FrOptionsAppliedService $optionsApplied,
    ) {}

    /**
     * Продукт для узла схемы (найденный по фильтру подбора).
     * Модель НЕ сохраняется — значения подменяются только для вывода/схемы.
     */
    public function assembleForNode(Product $productModel, array $getData): array
    {
        // сохранение базовых цен
        $basePrice = (float)$productModel->price;
        $baseDelivery = (float)$productModel->delivery;

        // поиск выбранных опций
        $option_applied = $this->optionsApplied->apply($getData, $productModel->productOption, $productModel->productOptionPrice);

        // изменение цены от опции товара, наименования и описания
        [$productModel->name,
        $productModel->description,
        $productModel->price,
        $option_price_applied] = $this->replaceProduct->apply($productModel, $option_applied);

        // применение правила цены (автоматически по опциям продукта)
        $calc = $this->priceRules->apply($productModel, $option_applied);
        $applied_rules = $calc['applied_rules'];

        // хэш по опциям — для подсчёта количества одинаковых продуктов
        $productModel->hash = $this->makeHash($option_applied + $applied_rules + ['manufacturer' => $getData['manufacturer'] ?? '']);

        $this->fillTkpCalcDefaults($productModel);

        $product = $productModel->toArray();
        $product['price_base'] = $basePrice;
        $product['count'] = 1;
        $product['manufacturer'] = $getData['manufacturer'] ?? '';
        $product['delivery_base'] = $baseDelivery;
        $product['option_price_applied'] = $option_price_applied;
        $product['price_rules_applied'] = $applied_rules;
        $product['option_applied'] = $option_applied;
        $product['indicators_reliability'] = $this->indicatorsReliability();
        $product['currency_val'] = $this->currencyValue($product['currency']);

        return $product;
    }

    /**
     * Продукт для подключения (кабель) — строится из полей фильтра,
     * без поиска в каталоге.
     */
    public function assembleForConnection(array $getData): array
    {
        $productModel = new Product;

        $productModel->id = 0;
        $productModel->template_id = TemplateType::Cable->value;
        $productModel->name = $getData['name'];
        $productModel->description = 'Длинна: ' . $getData['length'] . 'м.';
        $productModel->currency = 'RUB';
        $productModel->price = $getData['price'];
        $productModel->delivery = 0;
        $productModel->engineering = $productModel->getEngineering();
        $productModel->drawing = '';

        $this->fillTkpCalcDefaults($productModel);

        // --- применяем правила цены (автоматически по опциям продукта) ---
        $basePrice = $productModel->price;
        $baseDelivery = $productModel->delivery;

        $calc = $this->priceRules->apply($productModel);

        // НЕ сохраняем, просто подменяем для вывода/схемы
        $productModel->price = $calc['price'];
        $productModel->delivery = $calc['delivery'];
        $applied_rules = $calc['applied_rules'];
        $option_applied = $getData;

        // хэш по опциям — для подсчёта количества одинаковых продуктов
        $productModel->hash = $this->makeHash(
            $option_applied + $applied_rules
            + ['manufacturer' => $getData['manufacturer']]
            + ['length' => $getData['length']]
        );

        $product = $productModel->toArray();

        $product['price_base'] = $basePrice;
        $product['count'] = 1;
        $product['delivery_base'] = $baseDelivery;
        $product['price_rules_applied'] = $applied_rules;
        $product['option_applied'] = $option_applied;
        $product['manufacturer'] = $getData['manufacturer'];
        $product['currency_val'] = $this->currencyValue($product['currency']);

        return $product;
    }

    private function fillTkpCalcDefaults(Product $productModel): void
    {
        foreach (self::TKP_CALC_DEFAULTS as $field => $value) {
            $productModel->{$field} = $value;
        }
    }

    private function currencyValue(?string $currency): float
    {
        return $currency == 'RUB' ? 1.0 : $this->banks->getValue($currency);
    }

    private function makeHash(array $options): string
    {
        return md5(json_encode($options, JSON_UNESCAPED_UNICODE));
    }

    private function indicatorsReliability(): array
    {
        return [
            [
                'group_name' => 'Показатели надежности',
                'indicators' => [
                    ['name' => 'Средняя наработка на отказ, не менее', 'value' => '50000 часов'],
                    ['name' => 'Среднее время ремонта, не более', 'value' => '20 минут'],
                    ['name' => 'Срок службы, не менее', 'value' => '20 лет'],
                    ['name' => 'Гарантийный срок эксплуатации', 'value' => '12 месяцев с момента ввода в эксплуатацию, но не более 18 месяцев с момента отгрузки оборудования'],
                ],
            ],
        ];
    }
}

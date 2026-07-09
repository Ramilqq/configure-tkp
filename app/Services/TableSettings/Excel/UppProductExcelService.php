<?php

namespace App\Services\TableSettings\Excel;

/**
 * Импорт/экспорт продуктов УПП (устройство плавного пуска).
 * Вся механика — в базовом классе, здесь только конфигурация УПП.
 */
class UppProductExcelService extends AbstractBlockProductExcelService
{
    /**
     * Бизнес-группы УПП.
     * Каждая группа = одна TemplateOption.
     * base = базовое значение опции
     * variants = связанные подполя/подварианты этой же опции
     */
    private const GROUPS = [
        'v_control' => [
            'template_name' => 'Напряжение оперативного питания',
            'template_key'  => 'v_control',

            'base' => [
                'value' => '[V_Control]',
            ],
            'variants' => [
                [
                    'value' => '[V_Control_220V_DC]',
                    'price' => '[Price_220V_DC]',
                ],
                [
                    'value' => '[V_Control_110V_AC]',
                    'price' => '[Price_110V_AC]',
                ],
                [
                    'value' => '[V_Control_110V_DC]',
                    'price' => '[Price_110V_DC]',
                ],
            ],
        ],

        'ip' => [
            'template_name' => 'Степень защиты IP',
            'template_key'  => 'ip',
            'base' => [
                'value' => '[IP]',
                'rename_title' => '[IP]',
            ],
            'variants' => [
                [
                    'value' => '[IP41]',
                    'price' => '[Price_IP41]',
                    'rename_title' => '[IP41]',
                ],
                [
                    'value' => '[IP42]',
                    'price' => '[Price_IP42]',
                    'rename_title' => '[IP42]',

                ],
                [
                    'value' => '[IP54]',
                    'price' => '[Price_IP54]',
                    'rename_title' => '[IP54]',

                ],
            ],
        ],

        'bypass_breaker' => [
            'template_name' => 'Байпасный выключатель',
            'template_key'  => 'bypass_breaker',

            'variants' => [
                [
                    'value'  => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_Bypass_Breaker]',
                ],
            ],
        ],

        'service_smv' => [
            'template_name' => 'Способ обслуживания',
            'template_key'  => 'service_smv',

            'base' => [
                'value'     => '[Service_SMV]',
            ],
            'variants' => [
                [
                    'value'     => '[One_Service]',
                    'price'     => '[Price_One_Service]',
                    'dimension' => '[Dimension_One_Service]',
                    'weight'    => '[Weight_One_Service]',
                    'drawing'   => '[Drawing_One_Service]',
                ],
            ],
        ],

        'interface' => [
            'template_name' => 'Интерфейс',
            'template_key'  => 'interface',
            'base' => [
                'value'  => '[Interface]',
                'rename_title' => '[Interface_S]',
                'rename_description' => '[Interface]',
            ],
            'variants' => [
                [
                    'value'  => '[Profibus]',
                    'price'  => '[Price_Profibus]',
                ],
                [
                    'value'  => '[ModbusTCP]',
                    'price'  => '[Price_ModbusTCP]',
                ],
                [
                    'value'  => '[Profinet]',
                    'price'  => '[Price_Profinet]',
                ],
            ],
        ],

        'motor_type' => [
            'template_name' => 'Тип ЭД',
            'template_key'  => 'motor_type',
            'base' => [
                'value'  => 'A',
                'rename_description' => '[Motor_type_full]',
            ],
            'variants' => [
                [
                    'value'  => 'S',
                    'price'  => '[Price_Motor_type_Syn]',
                    'rename_description' => '[Motor_type_full_Syn]',
                ],
            ],
        ],

        'motor_reverse' => [
            'template_name' => 'Реверс двигателя (Секция реверса)',
            'template_key'  => 'motor_reverse',

            'variants' => [
                [
                    'value'  => 'Нет',
                ],
                [
                    'value'  => 'Да',
                    'price'  => '[Price_Reverse]',
                    'drawing' => '[Drawing_Reverse]',
                    'service' => '[Service_Reverse]',
                    'dimension' => '[Dimension_Reverse]',
                    'weight' => '[Weight_Reverse]',
                ],
            ],
        ],

        'cascade' => [
            'template_name' => 'Каскадный пуск (Секция коммутации)',
            'template_key'  => 'cascade',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_Cascade]',
                    'drawing'   => '[Drawing_Cascade]',
                    'dimension' => '[Dimension_Cascade]',
                    'weight'    => '[Weight_Cascade]',
                    'service'   => '[Service_Cascade]',
                ],
            ],
        ],

        'line_switch' => [
            'template_name' => 'Линейный выключатель (Встроен в корпус УПП)',
            'template_key'  => 'line_switch',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_Line_CB]',
                    'drawing'   => '[Drawing_Line_CB]',
                    'dimension' => '[Dimension_Line_CB]',
                    'weight'    => '[Weight_Line_CB]',
                    'service'   => '[Service_Line_CB]',
                ],
            ],
        ],

        'smv_series' => [
            'template_name' => 'Серия УПП',
            'template_key'  => 'smv_series',

            'base' => [
                'value'      => '[SMV_Series]',
                'rename_title'     => '[SMV_Series_Start]',
            ]
        ],

        'wsk' => [
            'template_name' => 'Контроллер температуры и влажности',
            'template_key'  => 'wsk',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_WSK]',
                ],
            ],
        ],
    ];

    /**
     * Технические поля, которые можно сохранить как обычные scalar-опции.
     * Всё, что входит в GROUPS, сюда НЕ попадает.
     */
    private const SCALAR_OPTION_MAP = [
        '[V_input]'                => ['name' => 'Входное напряжение, В', 'key' => 'v_input'],
        '[I_rated]'                => ['name' => 'Номинальный ток, А', 'key' => 'i_rated'],
        '[P_Output]'               => ['name' => 'Мощность подключаемого электродвигателя, кВт', 'key' => 'p_output'],
        '[Count_power_thyristors]' => ['name' => 'Кол-во силовых тиристоров УПП', 'key' => 'count_power_thyristors'],
        '[Bypass]'                 => ['name' => 'Тип байпаса', 'key' => 'bypass'],
        '[Drawing]'                => ['name' => 'Стандартный чертеж УПП', 'key' => 'drawing_default'],
        '[Dimension_SMV]'          => ['name' => 'Габаритные размеры стандартного УПП, мм', 'key' => 'dimension_smv_standard'],
        '[SMV_Weight]'             => ['name' => 'Масса, кг', 'key' => 'smv_weight'],
    ];

    protected function groups(): array
    {
        return self::GROUPS;
    }

    protected function scalarOptionMap(): array
    {
        return self::SCALAR_OPTION_MAP;
    }

    protected function priceTechKey(): string
    {
        return '[Price_SMV]';
    }

    protected function importMode(): string
    {
        return 'UPP';
    }

    protected function hashSignature(array $rowByTech): array
    {
        return [
            'v_input'               => (string)($rowByTech['[V_input]'] ?? ''),
            'i_rated'               => (string)($rowByTech['[I_rated]'] ?? ''),
            'p_output'              => (string)($rowByTech['[P_Output]'] ?? ''),
            'v_control'             => (string)($rowByTech['[V_Control]'] ?? ''),
            'count_power_thyristors'=> (string)($rowByTech['[Count_power_thyristors]'] ?? ''),
            'bypass'                => (string)($rowByTech['[Bypass]'] ?? ''),
            'motor_type'            => (string)($rowByTech['[Motor_type_full]'] ?? ''),
        ];
    }

    protected function previewExtraColumns(array $rowByTech): array
    {
        return [
            'P_Output'    => $rowByTech['[P_Output]'] ?? null,
            'V_input'     => $rowByTech['[V_input]'] ?? null,
            'I_rated'     => $rowByTech['[I_rated]'] ?? null,
        ];
    }

    protected function headerRowValues(): array
    {
        return [
            5  => 'Наименование',
            6  => 'Описание',
            7  => 'Входное напряжение, В',
            8  => 'Номинальный ток, А',
            9  => 'Мощность подключаемого электродвигателя, кВт',
            10 => 'Тип двигателя',
            11 => 'Напряжение оперативного питания',
            12 => 'Кол-во силовых тиристоров в УПП',
            13 => 'Базовая цена УПП с упаковкой',
            14 => 'Серия',
            16 => 'Степень защиты IP',
            17 => 'Тип байпаса',
            18 => 'Интерфейс',
            19 => 'Стандартный чертеж УПП',
            20 => 'Способ обслуживания',
            21 => 'Габаритные размеры стандартного УПП, мм (ДхГхВ]',
            22 => 'Масса, кг',

            24 => 'Опция 1.1: 220В DC',
            26 => 'Опция 1.2: 110В AC',
            28 => 'Опция 1.3: 110В DC',
            30 => 'Опция 2.1: IP41',
            32 => 'Опция 2.2: IP42',
            34 => 'Опция 2.3: IP54',
            36 => 'Опция 3: Байпасный выключатель',
            38 => 'Опция 4: Односторонний',
            43 => 'Опция 5.1: RS-485, Profibus',
            45 => 'Опция 5.2: Ethernet, Modbus TCP',
            47 => 'Опция 5.3: Ethernet, Profinet',
            49 => 'Опция 6: Синхронный',
            51 => 'Опция 7: Реверс двигателя',
            56 => 'Опция 8: Каскадный пуск',
            61 => 'Опция 9: Линейный выключатель',
            67 => 'Опция 10: Контроллер температуры и влажности',
        ];
    }

    protected function labelRowValues(): array
    {
        return [
            5  => 'Наименование формируется исходя из выбранного ВПЧ',
            6  => 'Описание формируется исходя из выбранного ВПЧ',
            7  => '[V_input]',
            8  => '[I_rated]',
            9  => '[P_Output]',
            10 => '[Motor_type_full]',
            11 => '[V_Control]',
            12 => '[Count_power_thyristors]',
            13 => '[Price_SMV]',
            14 => '[SMV_Series]',
            15 => '[SMV_Series_Start]',
            16 => '[IP]',
            17 => '[Bypass]',
            18 => '[Interface]',
            19 => '[Drawing]',
            20 => '[Service_SMV]',
            21 => '[Dimension_SMV]',
            22 => '[SMV_Weight]',

            24 => '[Price_220V_DC]',
            25 => '[V_Control_220V_DC]',
            26 => '[Price_110V_AC]',
            27 => '[V_Control_110V_AC]',
            28 => '[Price_110V_DC]',
            29 => '[V_Control_110V_DC]',
            30 => '[Price_IP41]',
            31 => '[IP41]',
            32 => '[Price_IP42]',
            33 => '[IP42]',
            34 => '[Price_IP54]',
            35 => '[IP54]',
            36 => '[Price_Bypass_Breaker]',
            37 => '[Bypass_Breaker]',
            38 => '[Price_One_Service]',
            39 => '[Drawing_One_Service]',
            40 => '[One_Service]',
            41 => '[Dimension_One_Service]',
            42 => '[Weight_One_Service]',
            43 => '[Price_Profibus]',
            44 => '[Profibus]',
            45 => '[Price_Modbus_TCP]',
            46 => '[Modbus_TCP]',
            47 => '[Price_Profinet]',
            48 => '[Profinet]',
            49 => '[Price_Motor_type_Syn]',
            50 => '[Motor_type_full_Syn]',
            51 => '[Price_Reverse]',
            52 => '[Drawing_Reverse]',
            53 => '[Service_Reverse]',
            54 => '[Dimension_Reverse]',
            55 => '[Weight_Reverse]',
            56 => '[Price_Cascade]',
            57 => '[Drawing_Cascade]',
            58 => '[Service_Cascade]',
            59 => '[Dimension_Cascade]',
            60 => '[Weight_Cascade]',
            61 => '[Price_Line_CB]',
            62 => '[Line_CB_Full]',
            63 => '[Drawing_Line_CB]',
            64 => '[Service_Line_CB]',
            65 => '[Dimension_Line_CB]',
            66 => '[Weight_Line_CB]',
            67 => '[Price_WSK]',
        ];
    }
}

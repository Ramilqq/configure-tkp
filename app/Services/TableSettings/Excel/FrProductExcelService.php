<?php

namespace App\Services\TableSettings\Excel;

/**
 * Импорт/экспорт продуктов ЧРП (частотно-регулируемый привод).
 * Вся механика — в базовом классе, здесь только конфигурация ЧРП.
 */
class FrProductExcelService extends AbstractBlockProductExcelService
{
    /**
     * Бизнес-группы ЧРП.
     * Каждая группа = одна TemplateOption.
     * base = базовое значение опции
     * variants = связанные подполя/подварианты этой же опции
     */
    private const GROUPS = [
        'material_trans' => [
            'template_name' => 'Материал обмоток ТН ВПЧ',
            'template_key'  => 'material_trans',
            'base' => [
                'value' => '[Material_trans]',
            ],
            'variants' => [
                [
                    'value' => '[Material_trans_AlumTrans]',
                    'price' => '[Price_VFD_AlumTrans]',
                ],
            ],
        ],

        'power_cell_bypass' => [
            'template_name' => 'Байпас неисправной силовой ячейки',
            'template_key'  => 'power_cell_bypass',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Механический',
                    'price'     => '[Price_VFD_mechBypass]',
                    'drawing'   => '[Drawing_VFD_mechBypass]',
                    'airflow'   => '[Airflow_rate_VFD_mechBypass]',
                    'dimension' => '[Dimension_VFD_mechBypass]',
                    'weight'    => '[VFD_mechBypass_Weight]',
                    'service'   => '[Service_VFD_mechBypass]',
                ],
            ],
        ],

        'sync_to_grid' => [
            'template_name' => 'Синхронизация на сеть',
            'template_key'  => 'sync_to_grid',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_SynchrOption]',
                    'drawing'   => '[Drawing_Reactor]',
                    'dimension' => '[Dimension_Reactor]',
                    'airflow'   => '[Airflow_rate_Reactor]',
                    'service'   => '[Service_Reactor]',
                    'weight'    => '[Weight_reactor]',
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
            ],
        ],

        'precharge' => [
            'template_name' => 'Предзаряд силовых ячеек',
            'template_key'  => 'precharge',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_PreCharge]',
                    'drawing'   => '[Drawing_PreCharge]',
                    'dimension' => '[Dimension_PreCharge]',
                    'service'   => '[Service_PreCharge]',
                    'weight'    => '[Weight_PreCharge]',
                ],
            ],
        ],

        'vfd_series' => [
            'template_name' => 'Серия ВПЧ',
            'template_key'  => 'vfd_series',
            'base' => [
                'value'      => '[VFD_Series]',
                'rename_title'     => '[VFD_Series_Start]',
                'rename_title_end' => '[VFD_Series_End]',
            ],
            'variants' => [
                [
                    'value'  => '[VFD_Series (Minprom)]',
                    'rename_title'     => '[VFD_Series_Start (Minprom)]',
                    'rename_title_end' => '[VFD_Series_End]',
                ],
            ],
        ],


        'motor_type' => [
            'template_name' => 'Тип ЭД',
            'template_key'  => 'motor_type',
            'base' => [
                'value'  => '[Motor_type]',
                'rename_title' => '[Motor_type]',
                'rename_description' => '[Motor_type_full]',
            ],
            'variants' => [
                [
                    'value'  => '[Motor_type_Syn]',
                    'rename_title' => '[Motor_type_Syn]',
                    'rename_description' => '[Motor_type_full_Syn]',

                ],
            ],
        ],

        'plc_syn' => [
            'template_name' => 'Наличие ПЛК управления системой возбуждения',
            'template_key'  => 'plc_syn',
            'variants' => [
                [
                    'value' => 'Нет',
                ],
                [
                    'value' => 'Да',
                    'price' => '[Price_PLC_Syn]',
                ],
            ],
        ],

        'bypass_vfd' => [
            'template_name' => 'Байпас ВПЧ',
            'template_key'  => 'bypass_vfd',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Авто. 3К3Р',
                    'price'     => '[Price_bypassVFD]',
                    'drawing'   => '[Drawing_bypassVFD]',
                    'dimension' => '[Dimension_bypassVFD]',
                    'weight'    => '[Weight_bypassVFD]',
                ],
                [
                    'value'     => 'Авто. 3К',
                    'price'     => '[Price_bypass2_VFD]',
                    'drawing'   => '[Drawing_bypass2VFD]',
                    'dimension' => '[Dimension_bypass2VFD]',
                    'weight'    => '[Weight_bypass2VFD]',
                ],
                [
                    'value'     => 'Ручной. 3Р',
                    'price'     => '[Price_bypass3_VFD]',
                    'drawing'   => '[Drawing_bypass3VFD]',
                    'dimension' => '[Dimension_bypass3VFD]',
                    'weight'    => '[Weight_bypass3VFD]',
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
                    'rename_title' => '[Profibus_S]',
                    'rename_description' => '[Profibus]',
                ],
                [
                    'value'  => '[ModbusTCP]',
                    'price'  => '[Price_ModbusTCP]',
                    'rename_title' => '[ModbusTCP_S]',
                    'rename_description' => '[ModbusTCP]',
                ],
                [
                    'value'  => '[Profinet]',
                    'price'  => '[Price_Profinet]',
                    'rename_title' => '[Profinet_S]',
                    'rename_description' => '[Profinet]',
                ],
            ],
        ],

        'plc_pt_100' => [
            'template_name' => 'ПЛК и датчики контроля температуры обмоток и подшипников ЭД',
            'template_key'  => 'plc_pt_100',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_PLC_Pt100]',
                ],
            ],
        ],

        'zip' => [
            'template_name' => 'ЗИП',
            'template_key'  => 'zip',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'F400',
                    'price'     => '[Price_Fan_F400]',
                ],
                [
                    'value'     => 'F400 Bypass',
                    'price'     => '[Price_Fan_F400_Bypass]',
                ],
                [
                    'value'     => 'F450',
                    'price'     => '[Price_Fan_F450]',
                ],
                [
                    'value'     => 'F450 Bypass',
                    'price'     => '[Price_Fan_F450_Bypass]',
                ],
                [
                    'value'     => 'F500',
                    'price'     => '[Price_Fan_F500]',
                ],
                [
                    'value'     => 'F560',
                    'price'     => '[Price_Fan_F560]',
                ],
                [
                    'value'     => 'F560 Bypass',
                    'price'     => '[Price_Fan_F560_Bypass]',
                ],
                [
                    'value'     => 'Price Set of boards',
                    'price'     => '[Price_Set_of_boards]',
                ],
                [
                    'value'     => 'PU',
                    'price'     => '[Price_PU]',
                    'weight'    => '[Current_PU]',
                ],
                [
                    'value'     => 'PU Bypass',
                    'price'     => '[Price_PU_Bypass]',
                    'weight'    => '[Current_PU_Bypass]',
                ],
            ],
        ],
    ];

    /**
     * Технические поля, которые можно сохранить как обычные scalar-опции.
     * Всё, что входит в GROUPS, сюда НЕ попадает.
     */
    private const SCALAR_OPTION_MAP = [
        '[S_trans]'                => ['name' => 'Номинальная полная мощность ТН, кВА', 'key' => 's_trans'],
        '[P_Output]'               => ['name' => 'Мощность подключаемого электродвигателя, кВт', 'key' => 'p_output'],
        '[V_input]'                => ['name' => 'Входное напряжение, В', 'key' => 'v_input'],
        '[V_output]'               => ['name' => 'Выходное напряжение, В', 'key' => 'v_output'],
        '[Freq_output]'            => ['name' => 'Выходная частота, Гц', 'key' => 'freq_output'],
        '[I_output]'               => ['name' => 'Выходной ток, А', 'key' => 'i_output'],
        '[Count_power_cell]'       => ['name' => 'Кол-во силовых ячеек ВПЧ', 'key' => 'count_power_cell'],
        '[Airflow_rate]'           => ['name' => 'Производительность вентиляторов охлаждения, м3/час', 'key' => 'airflow_rate'],
        '[Dimension_VFD_standart]' => ['name' => 'Габаритные размеры стандартного ЧРП, мм', 'key' => 'dimension_vfd_standard'],
        '[VFD_Weight]'             => ['name' => 'Масса, кг', 'key' => 'vfd_weight'],
        '[PrechargeFunction]'      => ['name' => 'Наличие функции предзаряда', 'key' => 'precharge_function'],
        '[PrechargeFunctionExec]'  => ['name' => 'Исполнение функции предзаряда', 'key' => 'precharge_function_exec'],
        '[Drawing]'                => ['name' => 'Стандартный чертеж ЧРП', 'key' => 'drawing_default'],
        '[Service_VFD]'            => ['name' => 'Способ обслуживания', 'key' => 'service_vfd'],
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
        return '[Price_VFD]';
    }

    protected function importMode(): string
    {
        return 'FR';
    }

    protected function hashSignature(array $rowByTech): array
    {
        return [
            's_trans'            => (string)($rowByTech['[S_trans]'] ?? ''),
            'p_output'           => (string)($rowByTech['[P_Output]'] ?? ''),
            'v_input'            => (string)($rowByTech['[V_input]'] ?? ''),
            'v_output'           => (string)($rowByTech['[V_output]'] ?? ''),
            'freq_output'        => (string)($rowByTech['[Freq_output]'] ?? ''),
            'i_output'           => (string)($rowByTech['[I_output]'] ?? ''),
            'count_power_cell'   => (string)($rowByTech['[Count_power_cell]'] ?? ''),
            'vfd_series'         => (string)($rowByTech['[VFD_Series]'] ?? ''),
            'motor_type'         => (string)($rowByTech['[Motor_type]'] ?? ''),
            'ip'                 => (string)($rowByTech['[IP]'] ?? ''),
            'interface'          => (string)($rowByTech['[Interface]'] ?? ''),
            'service_vfd'        => (string)($rowByTech['[Service_VFD]'] ?? ''),
        ];
    }

    protected function previewExtraColumns(array $rowByTech): array
    {
        return [
            'S_trans'     => $rowByTech['[S_trans]'] ?? null,
            'P_Output'    => $rowByTech['[P_Output]'] ?? null,
            'V_input'     => $rowByTech['[V_input]'] ?? null,
            'V_output'    => $rowByTech['[V_output]'] ?? null,
            'Count_cell'  => $rowByTech['[Count_power_cell]'] ?? null,
        ];
    }

    protected function enrichDerivedValues(array $rowByTech): array
    {
        $rowByTech = parent::enrichDerivedValues($rowByTech);

        $vOutput = (string)($rowByTech['[V_output]'] ?? '');

        $rowByTech['[V_output_name]'] = match ($vOutput) {
            '6000'  => '60',
            '10000' => '10',
            default => $vOutput,
        };

        $countPowerCell = (int)($rowByTech['[Count_power_cell]'] ?? 0);
        $rowByTech['[PWM_level]'] = $countPowerCell > 0
            ? (string)(2 * $countPowerCell + 1)
            : '';

        return $rowByTech;
    }

    protected function headerRowValues(): array
    {
        return [
            5  => 'Наименование',
            6  => 'Описание',
            7  => 'Номинальная полная мощность ТН, кВА',
            8  => 'Мощность подключаемого электродвигателя, кВт',
            9  => 'Входное напряжение, В',
            10 => 'Выходное напряжение, В',
            11 => 'Выходная частота, Гц',
            12 => 'Выходной ток, А',
            13 => 'Стандартный чертеж ЧРП',
            14 => 'Базовая цена ВПЧ',
            15 => 'Материал обмоток ТН ВПЧ',
            16 => 'Кол-во силовых ячеек ВПЧ',
            17 => 'Серия ВПЧ',
            20 => 'Тип ЭД',
            22 => 'Способ обслуживания',
            23 => 'IP',
            24 => 'Интерфейс',
            26 => 'Производительность вентиляторов охлаждения, м3/час',
            27 => 'Габаритные размеры стандартного ЧРП, мм',
            28 => 'Масса, кг',
            29 => 'Наличие функции предзаряда',
            30 => 'Исполнение функции предзаряда',
            32 => 'Опция 1: Материал обмоток ТН ВПЧ - Алюминий',
            34 => 'Опция 2.1: Байпас неисправной силовой ячейки (Механический)',
            40 => 'Опция 3: Синхронизация на сеть',
            46 => 'Опция 4.1: IP41',
            48 => 'Опция 4.2: IP42',
            50 => 'Опция 5: Предзаряд силовых ячеек',
            55 => 'Опция 6: Серия ВПЧ - Минпромторг',
            57 => 'Опция 7: Тип ЭД - синхронный',
            59 => 'Опция 7.1: Наличие ПЛК управления системой возбуждения',
            60 => 'Опция 8: Байпас ВПЧ (автоматический) (3 Контактора 3 разъединителя)',
            64 => 'Опция 8.2: Байпас ВПЧ (автоматический) (3 контактора)',
            68 => 'Опция 8.3: Байпас ВПЧ (Ручной) (3 разъединителя)',
            72 => 'Опция 10.1: Интерфейс = RS-485, Profibus',
            75 => 'Опция 10.2: Интерфейс = Ethernet, Modbus TCP',
            78 => 'Опция 10.3: Интерфейс = Ethernet, Profinet',
            86 => 'Опция 12: ПЛК и датчики контроля температуры обмоток и подшипников ЭД (8-10 датчиков PT100)',
            87 => 'ЗИП: Вентилятор охлаждения Платы управления Силовая ячейка',
        ];
    }

    protected function labelRowValues(): array
    {
        return [
            5  => 'Наименование формируется исходя из выбранного ВПЧ',
            6  => 'Описание формируется исходя из выбранного ВПЧ',
            7  => '[S_trans]',
            8  => '[P_Output]',
            9  => '[V_input]',
            10 => '[V_output]',
            11 => '[Freq_output]',
            12 => '[I_output]',
            13 => '[Drawing]',
            14 => '[Price_VFD]',
            15 => '[Material_trans]',
            16 => '[Count_power_cell]',
            17 => '[VFD_Series]',
            18 => '[VFD_Series_Start]',
            19 => '[VFD_Series_End]',
            20 => '[Motor_type]',
            21 => '[Motor_type_full]',
            22 => '[Service_VFD]',
            23 => '[IP]',
            24 => '[Interface_S]',
            25 => '[Interface]',
            26 => '[Airflow_rate]',
            27 => '[Dimension_VFD_standart]',
            28 => '[VFD_Weight]',
            29 => '[PrechargeFunction]',
            30 => '[PrechargeFunctionExec]',
            32 => '[Price_VFD_AlumTrans]',
            33 => '[Material_trans_AlumTrans]',
            34 => '[Price_VFD_mechBypass]',
            35 => '[Drawing_VFD_mechBypass]',
            36 => '[Airflow_rate_VFD_mechBypass]',
            37 => '[Dimension_VFD_mechBypass]',
            38 => '[VFD_mechBypass_Weight]',
            39 => '[Service_VFD_mechBypass]',
            40 => '[Price_SynchrOption]',
            41 => '[Drawing_Reactor]',
            42 => '[Dimension_Reactor]',
            43 => '[Airflow_rate_Reactor]',
            44 => '[Service_Reactor]',
            45 => '[Weight_reactor]',
            46 => '[IP41]',
            47 => '[Price_IP41]',
            48 => '[IP42]',
            49 => '[Price_IP42]',
            50 => '[Price_PreCharge]',
            51 => '[Drawing_PreCharge]',
            52 => '[Dimension_PreCharge]',
            53 => '[Service_PreCharge]',
            54 => '[Weight_PreCharge]',
            55 => '[VFD_Series (Minprom)]',
            56 => '[VFD_Series_Start (Minprom)]',
            57 => '[Motor_type_Syn]',
            58 => '[Motor_type_full_Syn]',
            59 => '[Price_PLC_Syn]',
            60 => '[Price_bypassVFD]',
            61 => '[Drawing_bypassVFD]',
            62 => '[Dimension_bypassVFD]',
            63 => '[Weight_bypassVFD]',
            64 => '[Price_bypass2_VFD]',
            65 => '[Drawing_bypass2VFD]',
            66 => '[Dimension_bypass2VFD]',
            67 => '[Weight_bypass2VFD]',
            68 => '[Price_bypass3_VFD]',
            69 => '[Drawing_bypass3VFD]',
            70 => '[Dimension_bypass3VFD]',
            71 => '[Weight_bypass3VFD]',

            72 => '[Profibus_S]',
            73 => '[Profibus]',
            74 => '[Price_Profibus]',
            75 => '[ModbusTCP_S]',
            76 => '[ModbusTCP]',
            77 => '[Price_ModbusTCP]',
            78 => '[Profinet_S]',
            79 => '[Profinet]',
            80 => '[Price_Profinet]',

            86 => '[Price_PLC_Pt100]',

            87 => '[Price_Fan_F400]',
            88 => '[Fan_F400]',
            89 => '[Price_Fan_F400_Bypass]',
            90 => '[Price_Fan_F450]',
            91 => '[Fan_F450]',
            92 => '[Price_Fan_F450_Bypass]',
            93 => '[Price_Fan_F500]',
            94 => '[Fan_F500]',
            95 => '[Price_Fan_F560]',
            96 => '[Fan_F560]',
            97 => '[Price_Fan_F560_Bypass]',
            98 => '[Price_Set_of_boards]',
            99 => '[Price_PU]',
            100 => '[Current_PU]',
            101 => '[Price_PU_Bypass]',
            102 => '[Current_PU_Bypass]',
        ];
    }
}

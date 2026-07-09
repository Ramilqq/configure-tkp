<?php

namespace App\Enums;

/**
 * Типы шаблонов продуктов с особой логикой (импорт/экспорт Excel,
 * стратегия поиска, страницы PDF). Значение — id записи в таблице templates.
 */
enum TemplateType: int
{
    /** Кабельная продукция — виртуальный тип для узлов без шаблона */
    case Cable = 0;

    /** ЧРП — частотно-регулируемый привод */
    case Fr = 1;

    /** УПП — устройство плавного пуска */
    case Upp = 4;

    public static function tryFromTemplateId(int|string|null $templateId): ?self
    {
        return $templateId === null ? null : self::tryFrom((int) $templateId);
    }

    public static function isFr(int|string|null $templateId): bool
    {
        return self::tryFromTemplateId($templateId) === self::Fr;
    }

    public static function isUpp(int|string|null $templateId): bool
    {
        return self::tryFromTemplateId($templateId) === self::Upp;
    }

    /**
     * Блочные шаблоны (ЧРП/УПП): импорт из Excel «по блокам»,
     * поиск продукта по подбору, страницы характеристик в PDF.
     */
    public static function isBlock(int|string|null $templateId): bool
    {
        $type = self::tryFromTemplateId($templateId);

        return $type === self::Fr || $type === self::Upp;
    }
}

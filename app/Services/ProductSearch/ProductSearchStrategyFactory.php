<?php

namespace App\Services\ProductSearch;

use App\Enums\TemplateType;

class ProductSearchStrategyFactory
{
    public function make(int $templateId): SearchStrategyInterface
    {
        return match (TemplateType::tryFromTemplateId($templateId)) {
            TemplateType::Fr    => new FrProductSearchStrategy($templateId),
            TemplateType::Upp   => new UppProductSearchStrategy($templateId),
            TemplateType::Cable => new CableProductSearchStrategy($templateId),
            default             => new GenericProductSearchStrategy($templateId),
        };
    }
}

<?php 
namespace App\Services\ProductSearch;

use Illuminate\Database\Eloquent\Builder;

interface SearchStrategyInterface
{
    // методы для получения информации о событии и представлении,
    // которые будут использоваться для отправки сообщений и отображения результатов поиска
    public function getEventMessage(): string;
    public function getEventUpdateFilter(): string;
    public function getEventSyncModalData(): string;
    // метод для получения названия представления,
    // которое будет использоваться для отображения результатов поиска и сообщений пользователю
    public function getView(): string;

    public function buildQuery(Builder $query, array $filterData): Builder;
    // формируем массив полей для фильтра,
    // которые будут отображаться в форме при открытии модального окна редактирования
    public function getDefaultFilterFields(array $savedFields): array;
    // формируем массив проверок, которые последовательно применяем к запросу
    // при диагностике причин отсутствия результатов,
    // а также для формирования альтернативных вариантов для корректировки фильтра
    public function getSearchChecks(array $getData): array;
    // применяем одну проверку из массива проверок для ЧРП к запросу,
    // добавляя условие whereHas с нужными параметрами
    public function applySearchCheck(Builder $query, array $check): Builder;
    // диагностируем причину отсутствия результатов при поиске ЧРП,
    // поэтапно применяя проверки и фиксируя количество оставшихся товаров,
    // а также формируем массив доступных значений для первой провальной проверки,
    // чтобы показать пользователю альтернативные варианты для корректировки фильтра
    public function diagnoseSearch(array $filterData): array;
    // формируем массив доступных значений для конкретной опции,
    // которая стала причиной отсутствия результатов,
    // чтобы показать пользователю альтернативные варианты для корректировки фильтра
    public function getAvailableOptionValues(array $productIds, string $relation, int $templateOptionId): array;
}













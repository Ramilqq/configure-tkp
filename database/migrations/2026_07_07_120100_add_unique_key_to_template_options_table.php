<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->mergeDuplicateTemplateOptions();

        Schema::table('template_options', function (Blueprint $table) {
            $table->unique(['template_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_options', function (Blueprint $table) {
            $table->dropUnique(['template_id', 'key']);
        });
    }

    /**
     * Схлопывает уже существующие дубли (template_id, key): оставляет запись
     * с наименьшим id, переносит непустые значения product_options на неё,
     * удаляет дубли (их product_options удалятся каскадно по FK).
     * Работает через DB::table(), чтобы не триггерить Model::booted()
     * (который на create() плодит product_options для всех товаров шаблона).
     */
    private function mergeDuplicateTemplateOptions(): void
    {
        $groups = DB::table('template_options')
            ->select('template_id', 'key')
            ->whereNotNull('key')
            ->groupBy('template_id', 'key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table('template_options')
                ->where('template_id', $group->template_id)
                ->where('key', $group->key)
                ->orderBy('id')
                ->get(['id']);

            $survivorId = (int)$rows->first()->id;
            $dupeIds = $rows->skip(1)->pluck('id')->map(fn($v) => (int)$v)->all();

            if (empty($dupeIds)) {
                continue;
            }

            $survivorValues = DB::table('product_options')
                ->where('template_option_id', $survivorId)
                ->pluck('value', 'product_id');

            $dupeOptions = DB::table('product_options')
                ->whereIn('template_option_id', $dupeIds)
                ->get(['product_id', 'value']);

            foreach ($dupeOptions as $po) {
                $currentValue = $survivorValues[$po->product_id] ?? null;

                if (($currentValue === null || $currentValue === '') && $po->value !== null && $po->value !== '') {
                    $survivorRowExists = DB::table('product_options')
                        ->where('template_option_id', $survivorId)
                        ->where('product_id', $po->product_id)
                        ->exists();

                    if ($survivorRowExists) {
                        DB::table('product_options')
                            ->where('template_option_id', $survivorId)
                            ->where('product_id', $po->product_id)
                            ->update(['value' => $po->value, 'updated_at' => now()]);
                    } else {
                        DB::table('product_options')->insert([
                            'template_option_id' => $survivorId,
                            'product_id' => $po->product_id,
                            'value' => $po->value,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $survivorValues[$po->product_id] = $po->value;
                }
            }

            DB::table('template_options')->whereIn('id', $dupeIds)->delete();
        }
    }
};

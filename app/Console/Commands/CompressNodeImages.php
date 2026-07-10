<?php

namespace App\Console\Commands;

use App\Models\Configuration\Node;
use App\Services\Configuration\NodeImageCompressor;
use Illuminate\Console\Command;

/**
 * Пережимает картинки узлов конфигуратора в БД (см. NodeImageCompressor).
 * Запускать после заливки тяжёлых иконок: php artisan nodes:compress-images
 */
class CompressNodeImages extends Command
{
    protected $signature = 'nodes:compress-images {--dry-run : Только показать, что будет сжато, без записи}';

    protected $description = 'Пережать картинки узлов конфигуратора (убрать мета-данные из встроенных растров)';

    public function handle(NodeImageCompressor $compressor): int
    {
        $dryRun = (bool)$this->option('dry-run');
        $totalBefore = 0;
        $totalAfter = 0;
        $changed = 0;

        foreach (Node::query()->orderBy('id')->get() as $node) {
            $before = strlen($node->image ?? '');
            if ($before === 0) {
                continue;
            }

            $compressed = $compressor->compressDataUri($node->image);
            $after = strlen($compressed);

            $totalBefore += $before;
            $totalAfter += $after;

            if ($after >= $before) {
                continue;
            }

            $changed++;
            $this->line(sprintf(
                '%s: %d КБ -> %d КБ%s',
                $node->title,
                (int)round($before / 1024),
                (int)round($after / 1024),
                $dryRun ? ' (dry-run, не сохранено)' : ''
            ));

            if (!$dryRun) {
                $node->image = $compressed;
                $node->save();
            }
        }

        $this->info(sprintf(
            'Итого: %d узлов сжато, %.2f МБ -> %.2f МБ',
            $changed,
            $totalBefore / 1024 / 1024,
            $totalAfter / 1024 / 1024
        ));

        return self::SUCCESS;
    }
}

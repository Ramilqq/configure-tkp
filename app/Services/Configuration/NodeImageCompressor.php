<?php

namespace App\Services\Configuration;

/**
 * Сжатие картинок узлов конфигуратора (data-uri).
 *
 * Картинки встраиваются base64 прямо в HTML страницы конфигуратора,
 * поэтому их вес напрямую определяет вес страницы. Экспортированные из
 * редакторов файлы часто несут сотни КБ мета-данных (EXIF/XMP) при иконке
 * в ~100 пикселей — перекодирование через GD убирает весь этот балласт.
 *
 * У SVG пережимаются встроенные растры (<image xlink:href="data:...">),
 * векторная часть не меняется. Растровые data-uri перекодируются целиком.
 * При любой ошибке возвращается исходное значение.
 */
class NodeImageCompressor
{
    /** Качество перекодированного JPEG */
    private const JPEG_QUALITY = 85;

    public function compressDataUri(string $dataUri): string
    {
        if (!preg_match('#^data:(image/[a-z+.\-]+);base64,(.+)$#s', $dataUri, $m)) {
            return $dataUri;
        }

        [, $mime, $payload] = $m;

        $binary = base64_decode($payload, true);
        if ($binary === false) {
            return $dataUri;
        }

        if ($mime === 'image/svg+xml') {
            $compressed = $this->compressSvg($binary);
        } else {
            $compressed = $this->compressRaster($binary);
        }

        if ($compressed === null || strlen($compressed) >= strlen($binary)) {
            return $dataUri;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($compressed);
    }

    /** Пережимает растры, встроенные в SVG через data-uri */
    private function compressSvg(string $svg): ?string
    {
        $changed = false;

        $result = preg_replace_callback(
            '#((?:xlink:)?href=")data:image/(jpeg|jpg|png);base64,([^"]+)"#',
            function ($m) use (&$changed) {
                $binary = base64_decode($m[3], true);
                if ($binary === false) {
                    return $m[0];
                }

                $compressed = $this->compressRaster($binary);
                if ($compressed === null || strlen($compressed) >= strlen($binary)) {
                    return $m[0];
                }

                $changed = true;
                $mime = $this->looksLikePng($compressed) ? 'png' : 'jpeg';

                return $m[1] . 'data:image/' . $mime . ';base64,' . base64_encode($compressed) . '"';
            },
            $svg
        );

        return ($result !== null && $changed) ? $result : null;
    }

    /**
     * Перекодирует растр через GD: убирает мета-данные, сохраняет размеры.
     * PNG с прозрачностью остаётся PNG, остальное — JPEG.
     */
    private function compressRaster(string $binary): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }

        $info = @getimagesizefromstring($binary);
        if ($info === false) {
            return null;
        }

        $img = @imagecreatefromstring($binary);
        if ($img === false) {
            return null;
        }

        $isPng = ($info['mime'] ?? '') === 'image/png';

        ob_start();
        if ($isPng) {
            imagesavealpha($img, true);
            imagepng($img, null, 9);
        } else {
            imagejpeg($img, null, self::JPEG_QUALITY);
        }
        $out = ob_get_clean();
        imagedestroy($img);

        return $out !== false && $out !== '' ? $out : null;
    }

    private function looksLikePng(string $binary): bool
    {
        return str_starts_with($binary, "\x89PNG");
    }
}

<?php

namespace App\Services\Pdf;

use App\Enums\TemplateType;
use App\Models\Configuration\Configuration;
use App\Models\TableSettings\GroupOption;
use App\Models\Tkp\Tkp;
use App\Services\TableSettings\DimensionSchemeResolver;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Сборка PDF технико-коммерческого предложения:
 * титульный лист, таблица цен, схема конфигурации и, для узлов ЧРП,
 * страницы информации/подключения/характеристик.
 */
class TkpPdfRenderer
{
    public function __construct(
        private DimensionSchemeResolver $schemeResolver,
        private FrSpecSheetBuilder $specSheetBuilder,
    ) {}

    /** @return string бинарное содержимое PDF */
    public function render(Tkp $tkp): string
    {
        $user = $tkp->user()->toArray();
        $tkpArr = $tkp->toArray();

        $configuration = Configuration::where('tkp_version', $tkp->tkp_version)->firstOrFail()->toArray();

        $groupOptions = collect(GroupOption::all()->toArray());

        // --- схемы габаритов (картинки) + параметры таблицы характеристик ЧРП ---
        $dimensionSchemes = [];

        foreach (($configuration['saved_schema']['nodes'] ?? []) as $key => $node) {
            $pid = $node['product']['hash'] ?? null;

            if (!$pid) continue;
            if (array_key_exists($pid, $dimensionSchemes)) continue;

            $schemes = $this->schemeResolver->resolveForNode($node);

            if (TemplateType::isFr($node['template_id'])) {
                $configuration['saved_schema']['nodes'][$key]['product']['table_params'] =
                    $this->specSheetBuilder->build($node['product']['option_applied']);
            } else {
                $configuration['saved_schema']['nodes'][$key]['product']['table_params'] = [];
            }

            foreach ($schemes as $scheme) {
                $arr = $scheme->toArray();
                $arr['images'] = array_map(function ($img) {
                    $img['abs_path'] = public_path($img['file_path']);
                    return $img;
                }, $arr['images'] ?? []);
                $dimensionSchemes[$pid][] = $arr;
            }

            if (!$schemes) {
                $dimensionSchemes[$pid] = null;
            }
        }

        $content = $this->buildHtml($user, $tkpArr, $configuration, $groupOptions, $dimensionSchemes);

        return $this->htmlToPdf($content);
    }

    private function buildHtml(
        array $user,
        array $tkp,
        array $configuration,
        $groupOptions,
        array $dimensionSchemes
    ): string {
        // титульный лист
        $content = view('pdf.title', compact('tkp'))->render();
        // страница с таблицей цен и характеристик
        $content .= view('pdf.table', compact('user', 'tkp', 'configuration'))->render();
        // страница с конфигурацией/схемой подключения
        $content .= view('pdf.configuration', compact('user', 'tkp', 'configuration'))->render();

        $node_repeat = [];

        foreach ($configuration['saved_schema']['nodes'] ?? [] as $node) {
            if (!isset($node['product']['hash'])) continue;
            // пропускаем повторяющиеся продукты (по хэшу) — чтобы не дублировать страницы для одинаковых продуктов
            $pid = $node['product']['hash'];
            if (in_array($pid, $node_repeat, true)) {
                continue;
            }
            $node_repeat[] = $pid;
            // для продуктов ЧРП добавляем страницы информации, подключения и характеристик
            if (TemplateType::isFr($node['template_id'])) {
                $content .= view('pdf.fr.info', compact('user', 'tkp', 'node'))->render();
                $content .= view('pdf.fr.connection', compact('user', 'tkp', 'node'))->render();
                $content .= view('pdf.fr.technical', compact('user', 'tkp', 'node', 'groupOptions', 'dimensionSchemes'))->render();
            }
        }
        // заключительная страница
        $content .= view('pdf.end_page', [])->render();

        return $content;
    }

    private function htmlToPdf(string $content): string
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');
        $html2pdf->writeHTML($content);

        return $html2pdf->output('TKP.pdf', 'S');
    }
}

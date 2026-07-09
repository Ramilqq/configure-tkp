<?php

namespace App\Http\Controllers;

use App\Models\Tkp\Tkp;
use App\Services\Pdf\TkpPdfRenderer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spipu\Html2Pdf\Html2Pdf;

class PdfController extends Controller
{
    use AuthorizesRequests;

    public function preview()
    {
        $data = [
            'items' => [
                ['#' => 1, 'name' => 'Товар А', 'qty' => 2, 'price' => 1200],
                ['#' => 2, 'name' => 'Товар Б', 'qty' => 1, 'price' => 5800],
                ['#' => 3, 'name' => 'Очень длинное название товара В', 'qty' => 7, 'price' => 250],
            ],
            'total' => 1200*2 + 5800 + 7*250,
        ];

        $content = view('pdf.preview', compact('data'))->render();

        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');

        $html2pdf->writeHTML($content);
        $pdf = $html2pdf->output('TKP.pdf', 'S');

        return $this->pdfResponse($pdf);
    }

    public function show($id, $tkp_version, TkpPdfRenderer $renderer)
    {
        $tkp = Tkp::where('tkp_version', $tkp_version)->firstOrFail();

        $this->authorize('view', $tkp);

        return $this->pdfResponse($renderer->render($tkp));
    }

    private function pdfResponse(string $pdf)
    {
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TKP.pdf"',
        ]);
    }
}

<?php



// app/Http/Controllers/PdfDemoController.php
namespace App\Http\Controllers;

use App\Models\Configuration\Configuration;
use App\Models\TableSettings\GroupOption;
use App\Models\Tkp\Tkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spipu\Html2Pdf\Html2Pdf;

class PdfController extends Controller
{

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
        return view('pdf.tkp', $data); // смотри в браузере
    }

    public function show($id, $tkp_version)
    {
        $content = '';

        $tkp = Tkp::where('tkp_version', $tkp_version)->first();
        $user = $tkp->user()->toArray();

        $tkp ? $tkp = $tkp->toArray() : exit('tkp not found');

        $configuration = Configuration::where('tkp_version', $tkp_version)->first();
        $configuration ? $configuration = $configuration->toArray() : exit('tkp not found');
        $groupOptions = GroupOption::all()->toArray();
        $groupOptions = collect($groupOptions);

        //dd($tkp);

        $content .= view('pdf.title', compact('tkp'))->render();
        $content .= view('pdf.table', compact('user', 'tkp', 'configuration'))->render();
        $content .= view('pdf.configuration', compact('user', 'tkp', 'configuration'))->render();

        //страница ифнормация чрп
        $content .= view('pdf.fr.info', compact('user', 'tkp', 'configuration'))->render();
        //страница подключения
        $content .= view('pdf.fr.connection', compact('user', 'tkp', 'configuration'))->render();
        //страница характеристик
        $content .= view('pdf.fr.technical', compact('user', 'tkp', 'configuration', 'groupOptions'))->render();


        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');
        
        $html2pdf->writeHTML($content);
        $pdf = $html2pdf->output('TKP.pdf', 'S');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TKP.pdf"',
        ]);

    }


}

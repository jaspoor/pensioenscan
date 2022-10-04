<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\TaxTable;

class ReportController extends Controller {

    public function index() {

        $reports = ReportDetail::all();

        return view('report/index', compact('reports'));
    }

    public function new() {

        return view('report/new');
    }

    public function add(Request $request) {

      $xml1 = $request->xml1->store('xml');
      $xml2 = $request->xml2->store('xml');

      $retirementDate = \DateTime::createFromFormat('d/m/Y', $request->get('retirementDate'));

      $report = new ReportDetail([
        'grossWage' => $request->get('grossWage'),
        'retirementDate' => $retirementDate,
        'xml1' => $xml1,
        'xml2' => $xml2 
      ]);

      $report->save();

      return redirect('/report');
    }
    
    public function pdf(Request $request, $id) {
        $reportDetail = ReportDetail::find($id);
        
        $theme = $request->get('theme', Report::DEFAULT_THEME);
        $report = Report::fromDetail($reportDetail);
        
        $pdf = Pdf::loadView('report/pdf', compact('report', 'theme'));
        
        return new Response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="report.pdf"'
        ]);
    }
}

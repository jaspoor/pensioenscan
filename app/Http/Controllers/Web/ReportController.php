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

        $theme = $request->get('theme', Report::DEFAULT_THEME);

        $grossWage = $request->get('grossWage');
        $retirementDate = \DateTime::createFromFormat('d/m/Y', $request->get('retirementDate'));
        $xml1 = $request->xml1->store('xml');
        $xml2 = $request->xml2->store('xml');
        $emailAddress = $request->get('emailAddress');

        $xml = simplexml_load_file(storage_path('app/' . $xml1));
        $fullName = (string) $xml->Gegevens->Naam;

        $reportDetail = new ReportDetail(compact('grossWage', 'retirementDate', 'emailAddress', 'fullName', 'xml1', 'xml2'));

        $report = Report::fromDetail($reportDetail);

        $reportDetail->filename = uniqid() . '.pdf';
        $pdf = Pdf::loadView('report/pdf', compact('report', 'theme'));
        $pdf->save(storage_path('app/pdf/' . $reportDetail->filename));

        $reportDetail->save();

        return redirect('/report');
    }
    
    public function rebuild(Request $request, $id) {
        $theme = $request->get('theme', Report::DEFAULT_THEME);
        $reportDetail = ReportDetail::find($id);

        $report = Report::fromDetail($reportDetail);

        $reportDetail->filename = uniqid() . '.pdf';
        $pdf = Pdf::loadView('report/pdf', compact('report', 'theme'));
        $pdf->save(storage_path('app/pdf/' . $reportDetail->filename));
        $reportDetail->save();
        
        return redirect('report');
    }

    public function downloadPdf(Request $request, $id) {
       
      $reportDetail = ReportDetail::find($id);
      $path = storage_path('app/pdf/' . $reportDetail->filename);
      $filename = sprintf('report_%d.pdf', $reportDetail->id);
    
      return response()->download($path, $reportDetail->filename, ['Content-Type' => 'application/pdf'], 'inline');
    }
  
    public function downloadXml1(Request $request, $id) {

      $reportDetail = ReportDetail::find($id);
      $path = storage_path('app/' . $reportDetail->xml1);
      $filename = $reportDetail->id . '_1.xml';
    
      return response()->download($path, $filename, ['Content-Type' => 'application/xml']);
    }
  
    public function downloadXml2(Request $request, $id) {
       
      $reportDetail = ReportDetail::find($id);
      $path = storage_path('app/' . $reportDetail->xml2);
      $filename = $reportDetail->id . '_2.xml';
    
      return response()->download($path, $filename, ['Content-Type' => 'application/xml']);
    }
}

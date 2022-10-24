<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportController extends Controller
{  
    /**
     * Generate a new report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {            
      $theme = $request->get('theme', Report::DEFAULT_THEME);
      $grossWage = $request->get('field:comp-l8imfdku');
      $retirementDate = $request->get('field:comp-l8imhl32');
      $xml1 = $request->file('field:comp-l8ikjksx')->store('xml');
      $emailAddress = $request->get('field:comp-l8ijfnkh');

      $retirementDate = \DateTime::createFromFormat('d/m/Y', $retirementDate);

      $xml = simplexml_load_file(storage_path('app/' . $xml1));
      $fullName = (string) $xml->Gegevens->Naam;

      $reportDetail = new ReportDetail(compact('grossWage', 'retirementDate', 'emailAddress', 'fullName', 'xml1'));
      
      $report = Report::fromDetail($reportDetail);

      $reportDetail->filename = uniqid() . '.pdf';
      $pdf = Pdf::loadView('report/pdf', compact('report', 'theme'));
      $pdf->save(storage_path('app/pdf/' . $reportDetail->filename));

      $reportDetail->save();
      
      return response()->json([
        'downloadUrl' => url(sprintf('/report/%d/pdf', $reportDetail->id))
      ], 200, [
        'Access-Control-Allow-Origin' => '*',
      ]);
    }
}
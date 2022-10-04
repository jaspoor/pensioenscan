<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use App\Models\ReportDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportController extends Controller
{  
    /**
     * Generate a new report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(ReportRequest $request)
    {
        try {
            $theme = $request->get('theme', Report::DEFAULT_THEME);
            $retirementDate = $request->get('field:comp-l8imhl32');
            $grossWage = $request->get('field:comp-l8imfdku');
            $xml1 = $request->file('field:comp-l8ikjksx')->store('xml');
            $emailAddress = $request->file('field:comp-l8ikjksx');

            $retirementDate = \DateTime::createFromFormat('d/m/Y', $retirementDate);

            $reportDetail = new ReportDetail(compact('grossWage', 'retirementDate', 'xml1'));
            $reportDetail->save();

            $report = Report::fromDetail($reportDetail);

            $filename = 'app/pdf/' . uniqid() . '.pdf';
            $pdf = Pdf::loadView('report/pdf', compact('report', 'theme'));
            $pdf->save(storage_path($filename));

          return response()->json([
            'id' => $filename,
            'email' => $emailAddress
          ], 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }
}
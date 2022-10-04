<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportController extends Controller
{

    /**
     * Test
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        return response()->json("Test success", 200);
    }
  
    /**
     * Generate a new report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(ReportRequest $request)
    {
        try {
          $data = $request->all();
          $file = $request->file('field:comp-l8ikjksx')->get();
          $xml = simplexml_load_string($file);

          $data['name'] = (string) $xml->Gegevens->Naam;
          $data['birthday'] = (string) $xml->Gegevens->Geboortedatum;
          $data['situation'] = (string) $xml->Gegevens->LevensSituatie;
          
          return response()->json($data, 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage}");
        }
    }
}
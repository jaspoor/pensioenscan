@extends('../layout')
@section('content')
@include('report/_partials/navbar', ['title' => 'PensioenScan overzicht'])

<div class="p-10">
  <div class="mb-10">
    <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url('/report/new')}}">Nieuw rapport</a>
  </div>

  <table class="table-auto border">
    <thead>
      <th class="text-left p-5 bg-slate-100 border-b">ID</th>
      <th class="text-left p-5 bg-slate-100 border-b">Date</th>
      <th class="text-left p-5 bg-slate-100 border-b">Name</th>
      <th class="text-left p-5 bg-slate-100 border-b">E-mail</th>
      <th class="text-left p-5 bg-slate-100 border-b">Retirement date</th>
      <th class="text-left p-5 bg-slate-100 border-b">XML (1)</th>
      <th class="text-left p-5 bg-slate-100 border-b">XML (2)</th>
      <th class="text-left p-5 bg-slate-100 border-b">PDF</th>
      <th class="text-left p-5 bg-slate-100 border-b"></th>
    </thead>
    <tbody>
      @foreach($reports as $report)
      <tr>
        <td class="text-left p-5">{{$report->id}}</td>
        <td class="text-left p-5">{{$report->created_at->format('Y-m-d H:i')}}</td>
        <td class="text-left p-5">{{$report->fullName}}</td>
        <td class="text-left p-5">{{$report->emailAddress}}</td>
        <td class="text-left p-5">{{date_create_from_format('Y-m-d H:i:s', $report->retirementDate)->format('Y-m-d')}}</td>
        <td class="text-left p-5">
          @if($report->xml1)
          <a class="text-blue-500" href="{{url("/report/{$report->id}/xml1")}}" target="_blank">xml1</a>
          @endif
        </td>
        <td class="text-left p-5">
          @if($report->xml2)
          <a class="text-blue-500" href="{{url("/report/{$report->id}/xml2")}}" target="_blank">xml2</a>
          @endif
        </td>
        <td class="text-left p-5">
          <a class="text-blue-500" href="{{url("/report/{$report->id}/pdf")}}" target="_blank">pdf</a>
        </td>
        <td class="text-left p-5">
          <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url("/report/{$report->id}/rebuild")}}">rebuild</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endsection
</div>
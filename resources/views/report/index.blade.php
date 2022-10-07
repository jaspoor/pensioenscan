@extends('../layout')
@section('content')
<h1 class="mb-10 text-xl">Rapporten overzicht</h1>

<div class="mb-10">
  <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url('/report/new')}}">Nieuw rapport</a>
</div>

<table class="table-auto border">
  <thead>
    <th class="text-left p-5 bg-slate-100 border-b">ID</th>
    <th class="text-left p-5 bg-slate-100 border-b">Date</th>
    <th class="text-left p-5 bg-slate-100 border-b">Name</th>
    <th class="text-left p-5 bg-slate-100 border-b">Pdf</th>
    <th class="text-left p-5 bg-slate-100 border-b"></th>
  </thead>
  <tbody>
    @foreach($reports as $report)
    <tr>
      <td class="text-left p-5">{{$report->id}}</td>
      <td class="text-left p-5">{{$report->created_at->format('Y-m-d H:i')}}</td>
      <td class="text-left p-5">{{$report->fullName}}</td>
      <td class="text-left p-5">
        <a class="text-blue-500" href="{{url("/report/download/{$report->filename}")}}">{{$report->filename}}</a>
      </td>
      <td class="text-left p-5">
        <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url("/report/{$report->id}/generate")}}">generate</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
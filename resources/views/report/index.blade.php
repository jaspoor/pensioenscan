@extends('../layout')
@section('content')
<h1 class="mb-10">Rapporten overzicht</h1>

<div class="mb-10">
  <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url('/report/new')}}">Nieuw rapport</a>
</div>

<table class="table-auto">
  <thead>
    <th class="text-left p-5">ID</th>
    <th class="text-left p-5">Name</th>
  </thead>
  <tbody>
    @foreach($reports as $report)
    <tr>
      <td class="text-left p-5">{{$report->id}}</td>
      <td class="text-left p-5">
        <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{url("/report/{$report->id}/pdf")}}"> genereer </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
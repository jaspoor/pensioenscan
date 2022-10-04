@extends('../layout')
@section('content')

<h1 class="mb-10">Rapport toevoegen</h1>

<form method="post" action="{{url('report/add')}}" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="mb-5">
      <label class="inline-block w-64">Huidig bruto inkomen (p/m)</label>
      <input type="number" class="border px-4 py-2" name="grossWage" required="required">
    </div>
    <div class="mb-5">
      <label class="inline-block w-64">Gewenste pensioendatum</label>
      <div class="datepicker relative inline-block">
        <input type="text"
          class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
          placeholder="Select a date" 
          name="retirementDate"
          required="required"/>
      </div>
    </div>
    <div class="mb-5">
      <label class="inline-block w-64">Pensioenoverzicht XML</label>
      <input type="file" class="border px-4 py-2" name="xml1" required="required">
    </div>
    <div class="mb-5">
      <label class="inline-block w-64">Pensioenoverzicht XML partner (optioneel)</label>
      <input type="file" class="border px-4 py-2" name="xml2">
    </div>

    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Toevoegen</button>
</form>
@endsection

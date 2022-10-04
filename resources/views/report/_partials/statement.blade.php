
<table class="table table-auto bordered mb-20 font-small statement-table">
    <tr>
    <th class="table-header"></th>
    @foreach(array_keys($statement->incomePerDate) as $dateKey)
    <th class="table-header text-right">
        {{ date('M-Y', strtotime($dateKey)) }}
        @if($statement->incomePerDate[$dateKey]['retirement'] ?? false)
        <sup>1</sup>
        @endif
    </th>
    @endforeach
    </tr>
    <tr>
    <td class="table-subheader text-left">{{$statement->fullName}}</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="table-subheader text-right">{{ $periodData['age'] }} jaar</td>
    @endforeach
    </tr>

    <tr>
    <td class="text-left">Salaris</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right">{{ number_format(round($periodData['wage'] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>

    <tr>
    <td class="text-left">AOW incl. vakantiegeld</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right">{{ number_format(round($periodData['AOW'] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>

    @foreach($statement->fundNames as $fundName)
    <tr>
    <td class="text-left">{{$fundName}}</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right">{{ number_format(round($periodData[$fundName] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>
    @endforeach

    <tr>
    <td class="text-left font-bold">Bruto Inkomen</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right font-bold">{{ number_format(round($periodData['total_gross'] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>

    <tr>
    <td class="text-left">Belastingen</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right">{{ number_format(round($periodData['total_tax'] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>
    </tr>

    <tr>
    <td class="text-left font-bold">Netto Inkomen</td>
    @foreach($statement->incomePerDate as $periodData)
    <td class="text-right font-bold">{{ number_format(round($periodData['total_nett'] ?? 0), 0, ',', '.') }}</td>
    @endforeach
    </tr>
</table>

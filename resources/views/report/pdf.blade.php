<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Pensioen Rapport</title>
  <link rel="stylesheet" href="themes/base.css" />
  <link rel="stylesheet" href="themes/{{$theme}}/style.css" />
</head>

<body style="width: 100%">

  <div class="header">
    <img src="themes/{{$theme}}/logo.png" class="logo" />

    <table class="table-auto my-5">
      <tr>
        <th class="text-left pr-5">Aan:</th>
        <td>{{$report->statement1->fullName}}</td>
      </tr>
      <tr>
        <th class="text-left pr-5">Van:</th>
        <td>Gert Bos</td>
      </tr>
      <tr>
        <th class="text-left pr-5">Datum:</th>
        <td>{{date('d-m-Y')}}</td>
      </tr>
      <tr>
        <th class="text-left pr-5">Onderwerp:</th>
        <td>Financiële gevolgen eerder stoppen met werken</td>
      </tr>
    </table>
  </div>

  <div class="container">

    <p class="mb-10">
      Geachte heer/mevrouw {{$report->statement1->lastName}},<br /><br />

      Naar aanleiding van de ontvangen stukken, heb ik op verzoek een aantal berekeningen gemaakt, waarbij ik de inkomenspositie weergeef zoals die ontstaat als je eerder stopt met werken en het pensioen in laat gaan.
      <br /><br />
      In dit persoonlijk Pensioen Rapport ben ik uitgegaan van de volgende uitgangspunten:
    </p>

    <strong>Uitgangspunten</strong>
    <table class="mb-20">
      <tr>
        <th class="pl-0">Geboortedatum {{$report->statement1->fullName}}:</th>
        <td>{{$report->statement1->birthdayDate->format('d-m-Y')}}</td>
      </tr>
      <tr>
        <th class="pl-0">AOW-datum {{$report->statement1->fullName}}:</th>
        <td>{{$report->statement1->aowDate->format('d-m-Y')}}</td>
      </tr>
      @if($report->statement1->retirementDate != $report->statement1->aowDate)
      <tr>
        <th class="pl-0">Gewenste pensioendatum {{$report->statement1->fullName}}:</th>
        <td>{{$report->statement1->retirementDate->format('d-m-Y')}}</td>
      </tr>
      @endif
      @if($report->statement2)
      <tr>
        <th class="pl-0">Geboortedatum {{$report->statement2->fullName}}:</th>
        <td>{{$report->statement2->birthdayDate->format('d-m-Y')}}</td>
      </tr>
      <tr>
        <th class="pl-0">AOW-datum {{$report->statement2->fullName}}:</th>
        <td>{{$report->statement2->aowDate->format('d-m-Y')}}</td>
      </tr>
      @if($report->statement2->retirementDate != $report->statement2->aowDate)
      <tr>
        <th class="pl-0">Gewenste pensioendatum {{$report->statement2->fullName}}:</th>
        <td>{{$report->statement2->retirementDate->format('d-m-Y')}}</td>
      </tr>
      @endif
      @endif
    </table>

    <strong>Huidige situatie</strong>
    <p class="mb-20">Hieronder is de huidige financiële situatie in een grafiek weergegeven. Dus nog niet anticiperend op een eventueel eerder stoppen met werken.</p>

    <div class="statement-chart page-break">
      <img src="data:image/png;base64,{{$report->chartData}}"/>
    </div>

    <p class="mb-20">De achterliggende cijfers zijn opgenomen in bijlage 1. De hierboven getoonde bedragen luiden in netto euro's.</p>

    <h1 class="page-break">Bijlage 1: Overzicht netto (pensioen)inkomen in de huidige situatie </h1>
    @include('report/_partials/statement', ['statement' => $report->statement1])

    @if($report->statement2)
    @include('report/_partials/statement', ['statement' => $report->statement2])
    @endif

    <p class="font-small">
        <sup>1</sup> Ingang (gewenst) pensioen
    </p>
  </div>

</body>

</html>
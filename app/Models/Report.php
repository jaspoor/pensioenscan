<?php

namespace App\Models;

use DateTime;
use QuickChart;

class Report
{
    const DEFAULT_THEME = 'kanikmetpensioen';
    
    public $statement1;
    public $statement2;
 
    public $fundNames;
    public $chartData;

    public static function fromDetail(ReportDetail $reportDetail): Report
    {        
        $report = new Report();       
        $report->statement1 = RetirementStatement::fromXml($reportDetail->xml1);

        if ($reportDetail->getRetirementDate()) {
            $report->statement1->setRetirementDate($reportDetail->getRetirementDate());
        }
      
        $report->statement1->addGrossWage($reportDetail->getGrossWage());

        if ($report->statement2) {
            $report->statement2 = RetirementStatement::fromXml($reportDetail->xml2);
        }

        $report->chartData = $report->buildChartData();

        return $report;
    }

    private function buildChartData(): string
    {

        $combinedDates = $this->statement1->getDates(); 
        if ($this->statement2) {
            $combinedDates = array_merge($combinedDates, $this->statement2->getDates());
        }

        sort($combinedDates);

        $labels = array_map(function($date) {
            return $date->format('M Y');
        }, $combinedDates);

        $labels[0] = 'Huidig inkomen';

        if ($this->statement2) {
            $statement2Totals = $this->statement2->getIncomeTotalsPerDate();
            $statement2CombinedTotals = [];
            $statement2Value = 0;
            foreach ($combinedDates as $date) {
                $dateKey = $date->format('Y-m-d');
                $statement2Value = $statement2Totals[$dateKey] ?? $statement2Value;
                $statement2CombinedTotals[] = $statement2Value;
            }
        }

        $statement1Totals = $this->statement1->getIncomeTotalsPerDate();
        $statement1CombinedTotals = [];
        $statement1Value = 0;
        foreach ($combinedDates as $date) {
            $dateKey = $date->format('Y-m-d');
            $statement1Value = $statement1Totals[$dateKey] ?? $statement1Value;
            $statement1CombinedTotals[] = $statement1Value;
        }

        $datasets = [];
        if ($this->statement2) {
            $datasets[] = [
                'label' => $this->statement2->fullName,
                'data' => $statement2CombinedTotals,
                'borderWidth' => 0,
                'backgroundColor' => '#7dd3fc'
            ];
        }

        $datasets[] = [
            'label' => $this->statement1->fullName,
            'data' => $statement1CombinedTotals,
            'borderWidth' => 0,
            'backgroundColor' => '#e0f2fe'
        ];

        $config = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets
            ],
            'options' => [
                'legend' => [
                    'reverse' => true,
                    'labels' => [
                        'fontSize' => 6
                    ]
                ],
                'scales' => [
                    'xAxes' => [[
                        'stacked' => true,
                        'ticks' => [
                            'fontSize' => 6
                        ]
                    ]],
                    'yAxes' => [[
                        'id' => 'y1',
                        'stacked' => true,
                        'ticks' => [
                            'fontSize' => 8,
                            'callback' => "function(val) { return val.toLocaleString('nl-NL'); }"
                        ]
                    ]]
                ], 
                'plugins' => [
                    'datalabels' => [
                        'labels' => [
                            'value' => [
                                'anchor' => 'end',
                                'align' => 'start',
                                'padding' => [
                                    'top' => 5
                                ],
                                'font' => [ 'size' => 8 ],
                                'color' => '#888',
                                'display' => 'auto',
                                'clip' => true,
                                'formatter' => "function(val) { return '€ ' + Math.round(val).toLocaleString('nl-NL'); }"
                            ],
                            'total' => [
                                'anchor' => 'end',
                                'align' => 'top',
                                'font' => [ 'size' => 8 ],
                                'color' => '#000',
                                'display' => "function(ctx) { return (ctx.datasetIndex === ctx.chart.data.datasets.length - 1); }",
                                'formatter' => "function(value, ctx) {    let total = 0;    let index = ctx.dataIndex;    ctx.chart.data.datasets.map((d, i) => {        if (i === 2) return;        total += d.data[index] || 0;    });    return '€ ' + Math.round(total).toLocaleString('nl-NL');}",
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $json = "{
            type: 'bar',
            data: {
                labels: ['Sep 2022', 'Mar 2023', 'Jan 2024', 'Mar 2025', 'Mar 2026'],
                datasets: [{
                    label: 'Partner',
                    yAxisID: 'y1',
                    data: [100, 200, 300, 400]
                },{
                    label: 'HJ Nijmans',
                    yAxisID: 'y1',
                    data: [5256,5487.78,527.75,1501.42,3417.83],
                    backgroundColor: '#5a9075'
                }]
            },
            options: {
                legend: {                    
                    reverse: true
                },
                scales: {
                xAxes: [{
                    stacked: true
                }],
                yAxes: [{
                    id: 'y1',
                    stacked: true,      
                    ticks: {
                    callback: (val) => {
                        return '€ ' + val.toLocaleString('nl-NL');
                    }
                    }
                }]
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#444',
                        formatter: (value) => {
                            return '€ ' + Math.round(value).toLocaleString('nl-NL');
                        }
                    }
                }
            }
        }";

        return $this->getChartBase64Binary($config);
    }

    private function getChartBase64Binary(array $chartConfig): string
    {
        $chart = new QuickChart(['height' => 250]);
        
        $json = json_encode($chartConfig);

        // Unquote functions to have them interpreted as such
        $json = preg_replace('/\"(function.+?)\"/m', '$1', $json);

        $chart->setConfig($json);

        return base64_encode($chart->toBinary());
    }
    
}

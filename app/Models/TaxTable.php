<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Throwable;

class TaxTable {
    
    protected $table;
    protected $beforeRetirement;
    protected $before1946andRetired;
    protected $after1946;

    public function calc(int $grossAmount, int $yearOfBirth, bool $isRetired): int
    {
        $this->setTable($yearOfBirth, $isRetired);
        
        $tax = 0;
        foreach ($this->table as $gross => $taxAmount) {
            
            if ($grossAmount > $gross) {
                $tax = $taxAmount;
                continue;
            }

            return $tax;
        }
        
        /* 
        Voor hogere tabellonen: neem 49,50% van het verschil tussen dit hogere loon en 9.112,50. 
        Rond het resultaat af op centen in het voordeel van de werknemer. 
        Tel dit bedrag op bij het bedrag aan inhouding dat hoort bij het tabelloon van 9.112,50
        in de kolom die van toepassing is op de werknemer.
        */

        $addition = round(.4950 * ($grossAmount-911250));
        $tax = end($this->table);

        return $tax + $addition;
    }

    private function setTable(int $yearOfBirth, bool $isRetired): void
    {
        if ($yearOfBirth <= 1945 && $isRetired) {
            $this->table = $this->before1946andRetired;
        }
        elseif ($yearOfBirth > 1945 && $isRetired) {
            $this->table = $this->after1946;
        }
        else {
            $this->table = $this->beforeRetirement;
        }
    }

    public static function load(): TaxTable
    {
        return self::fromFile('tabel-wit-2022.csv');
    } 

    public static function fromFile(string $filename): TaxTable
    {
        $content = Storage::disk('local')->get($filename);

        return self::fromCsv($content);
    }

    public static function fromCsv(string $content): TaxTable 
    {
        $table = new TaxTable;

        $records = [];
        $rowsToSkip = 175;
        $data = str_getcsv($content, "\r\n");
        $data = array_splice($data, $rowsToSkip, count($data)-($rowsToSkip+1));
        $data = array_map(function($s) { return str_replace("\n", "", $s); }, $data);
        
        foreach($data as &$line) {
            $line = str_replace('"', "", $line);
            $record = str_getcsv($line, " , ");
            $record = array_map(function($s) { return str_replace(",", "", $s); }, $record);
            $record = array_values(array_filter($record));
            

            try {
                list($grossAmount, , $beforeRetirementNettAmount, , , $before1946andRetiredNettAmount, , , $after1946NettAmount, ) = $record;

                $grossAmount = $grossAmount * 100;

                $table->beforeRetirement[$grossAmount] = $beforeRetirementNettAmount * 100;
                $table->before1946andRetired[$grossAmount] = $before1946andRetiredNettAmount * 100;
                $table->after1946[$grossAmount] = $after1946NettAmount * 100;
            } catch (Throwable $e) {
                echo $e->getMessage();
                dd($line, $record);
            }
        }

        return $table;
    }
}
<?php

namespace App\Models;

use App\Helpers\AowHelper;
use DateTime;
use SimpleXMLElement;

class RetirementStatement
{
    public $fullName;
    public $initials;
    public $lastName;
    public $birthdayDate;
    public $yearOfBirth;
    public $age;
    public $aowDate;
    public $retirementDate;
    public $hasPartner;
    public $grossWage;
    public $fundNames;
    public $incomePerDate;

    public static function fromXml(string $filename): RetirementStatement
    {        
        $file = storage_path('app/' . $filename);
        
        $xml = simplexml_load_file($file);

        $statement = new RetirementStatement;

        $statement->setName($xml);
        $statement->setBirthdayDate($xml);
        $statement->setPartner($xml);
        $statement->setAowAndRetirementDate();
        $statement->setYearOfBirth();
        $statement->setAge();
        $statement->setIncomeData($xml);

        return $statement;
    }

    public function getDates(): array {
        return array_map(function($dateKey) {
            return \DateTime::createFromFormat('Y-m-d', $dateKey);
        }, array_keys($this->incomePerDate));
    }

    public function getIncomeTotals(): array
    {
        return array_values($this->getIncomeTotalsPerDate());            
    }

    public function getIncomeTotalsPerDate(): array
    {
        return array_map(function($data) {
            return round($data['total_nett'], 2);
        }, $this->incomePerDate);            
    }

    public function getIncomePerDate(): array
    {
        return $this->incomePerDate;        
    }

    public function setRetirementDate(\DateTime $retirementDate): void
    {
        $this->retirementDate = $retirementDate;
    }

    private function setName(SimpleXMLElement $xml): void
    {
        $fullName = (string) $xml->Gegevens->Naam;
        
        $nameParts = explode(" ", $fullName);

        $this->fullName = $fullName;
        $this->initials = reset($nameParts);
        $this->lastName = str_replace($this->initials, "", $fullName);
    }

    private function setBirthdayDate(SimpleXMLElement $xml): void
    {
        $birthday = (string) $xml->Gegevens->Geboortedatum;

        $this->birthdayDate = \DateTime::createFromFormat("Y-m-d", $birthday);
    }

    private function setPartner(SimpleXMLElement $xml): void
    {
        $situation = (string) $xml->Gegevens->LevensSituatie;

        $this->hasPartner = ('GehuwdOfSamenwonend' === $situation);
    }

    private function setAowAndRetirementDate(): void
    {
        $aowDate = AowHelper::getAowDate($this->birthdayDate);

        $this->aowDate = $aowDate;
        $this->retirementDate = $aowDate;
    }

    private function setYearOfBirth(): void
    {
        $this->yearOfBirth = (int) $this->birthdayDate->format('Y');
    }

    private function setAge(): void
    {
        $this->age = (int) $this->getAgeAt(now());
    }

    private function getRetirementDetailFromDate(SimpleXMLElement $detailXml): \DateTime
    {        
        $fromYears = (int) $detailXml->Van->Jaren;
        $fromMonths = (int) $detailXml->Van->Maanden;
        $fromDate = clone $this->birthdayDate;
        $fromDate
            ->modify("+{$fromYears} years")
            ->modify("+{$fromMonths} months")
            ->modify("first day of next month");
        
        return $fromDate;
    }

    private function getAgeAt(\DateTime $dateTime): int
    {
        return  (int) $dateTime->diff($this->birthdayDate)->format('%Y');  
    }

    private function setIncomeData(SimpleXMLElement $statementXml): void
    {
        $fundNames = [];
        $incomePerDate = [];

        $retirementDetailsXml = $statementXml
            ->Details
            ->OuderdomsPensioenDetails
            ->OuderdomsPensioen;

        foreach ($retirementDetailsXml as $detailXml) 
        {
            $fromDate = $this->getRetirementDetailFromDate($detailXml);
            $dateKey = $fromDate->format('Y-m-d');
            $incomePerDate[$dateKey]['age'] = $this->getAgeAt($fromDate);

            $this->addAowIncome($detailXml, $dateKey, $incomePerDate);
            $this->addFundIncome($detailXml, $dateKey, $incomePerDate, $fundNames);
            $this->addIndicativeFundIncome($detailXml, $dateKey, $incomePerDate, $fundNames);            
        }
        
        $this->incomePerDate = $incomePerDate;
        $this->fundNames = array_unique($fundNames);

        $this->resetIncomeTotals();

    }

    public function resetIncomeTotals(): void
    {
        $taxTable = TaxTable::load();

        $incomePerDate = $this->incomePerDate;
        foreach ($incomePerDate as $dateKey => $funds) {
            $totalGross = 0;
            foreach ($funds as $fundName => $grossAmount) {
                if (in_array($fundName, ['age', 'AOW', 'total_gross', 'total_tax', 'total_nett'])) continue;
                $totalGross += $grossAmount;
            }

            $isRetired = DateTime::createFromFormat('Y-m-d', $dateKey) >= $this->aowDate;
            
            $incomeTax = $taxTable->calc($totalGross * 100, $this->yearOfBirth, $isRetired) / 100;

            $incomePerDate[$dateKey]['total_gross'] = $totalGross;
            $incomePerDate[$dateKey]['total_tax'] = $incomeTax;
            $incomePerDate[$dateKey]['total_nett'] = $totalGross - $incomeTax;
        }

        $this->incomePerDate = $incomePerDate;
    }
    
    public function addGrossWage(int $grossWage): void
    {
        $this->grossWage = $grossWage;

        $incomePerDate = $this->incomePerDate;

        $today = now();
        $dateKey = $today->format('Y-m-d');        

        $incomePerDate[$dateKey]['age'] = $this->age;
        $incomePerDate[$dateKey]['wage'] = $this->grossWage;

        // Add gross wage for every date until retirement
        foreach ($incomePerDate as $dateKey => $incomeData) {
            $date = \DateTime::createFromFormat('Y-m-d', $dateKey);
            
            if ($date < $this->retirementDate) {
                $incomePerDate[$dateKey]['wage'] = $this->grossWage;
            }
        }
        
        // Add income 'column' at exact moment of retirement
        foreach ($incomePerDate as $dateKey => $funds) {
            $date = DateTime::createFromFormat('Y-m-d', $dateKey);
            if ($date < $this->retirementDate) {
                $retirementDateKey = $this->retirementDate->format('Y-m-d');
                $incomePerDate[$retirementDateKey] = $funds;
                $incomePerDate[$retirementDateKey]['age'] = $this->getAgeAt($this->retirementDate);
                unset($incomePerDate[$retirementDateKey]['wage']);
                $incomePerDate[$retirementDateKey]['retirement'] = true;
            }
        }

        $this->incomePerDate = $incomePerDate;
        ksort($this->incomePerDate);

        $this->resetIncomeTotals();
    }

    private function addAowIncome(SimpleXMLElement $detailXml, string $dateKey, &$incomePerDate)
    {
        if (empty($detailXml->AOW))
            return;

        $fundName = 'AOW';
        $grossAmount = (int) $detailXml->AOW->AOWDetailsOpbouw->TeBereikenAlleenstaand;
        if ($this->hasPartner) {
            $grossAmount = (int) $detailXml->AOW->AOWDetailsOpbouw->TeBereikenSamenwonend;
        }

        $incomePerDate[$dateKey][$fundName] = $grossAmount / 12;
    }

    
    private function addFundIncome(SimpleXMLElement $detailXml, string $dateKey, &$incomePerDate, &$fundNames)
    {
        foreach ($detailXml->Pensioen as $retirementFund) {
            $fundName = (string) $retirementFund->PensioenUitvoerder;
            $grossAmount = (int) $retirementFund->TeBereiken;
            
            if (!isset($incomePerDate[$dateKey][$fundName])) {
                $incomePerDate[$dateKey][$fundName] = 0;
            }

            $fundNames[] = $fundName;
            $incomePerDate[$dateKey][$fundName] += $grossAmount / 12;
        }
    }

    private function addIndicativeFundIncome(SimpleXMLElement $detailXml, string $dateKey, &$incomePerDate, &$fundNames)
    {
        foreach ($detailXml->IndicatiefPensioen as $retirementFund) {
            $fundName = (string) $retirementFund->PensioenUitvoerder;
            $grossAmount = (int) $retirementFund->TeBereiken;
            
            if (!isset($incomePerDate[$dateKey][$fundName])) {
                $incomePerDate[$dateKey][$fundName] = 0;
            }

            $fundNames[] = $fundName;
            $incomePerDate[$dateKey][$fundName] += $grossAmount / 12;
        }
    }

    
}
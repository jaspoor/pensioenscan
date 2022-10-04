<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    use HasFactory;

    protected $fillable = ['grossWage', 'retirementDate', 'xml1', 'xml2'];

    public function getRetirementDate(): \DateTime
    {
        return $this->retirementDate instanceof \DateTime ? $this->retirementDate : \DateTime::createFromFormat("Y-m-d", $this->retirementDate);
    }

    public function getGrossWage(): int
    {
        return $this->grossWage;
    }
}

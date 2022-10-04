<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    use HasFactory;

    protected $fillable = ['grossWage', 'retirementDate', 'xml1', 'xml2'];

    public function getRetirementDate(): ?\DateTime
    {
      $date = \DateTime::createFromFormat("Y-m-d", $this->retirementDate);
      return $date ?: null;
    }

    public function getGrossWage(): int
    {
        return $this->grossWage;
    }
}

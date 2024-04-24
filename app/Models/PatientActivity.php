<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PatientActivity extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function dateHuman(): Attribute
    {
        return Attribute::make(
            get: fn(?Carbon $value) => Str::lower(($this->created_at ? $this->created_at : now())->isoFormat('llll')),
        );
    }

    protected function Protocol(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Str::ucfirst($value),
        );
    }
}

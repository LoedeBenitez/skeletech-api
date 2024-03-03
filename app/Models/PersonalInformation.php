<?php

namespace App\Models;

use App\Models\Dashboard\Memoranda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;
    protected $table = 'personal_informations';

    protected $fillable = [
        'email',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'alias',
        'gender',
        'birth_date',
        'age',
        'updated_by_id',
        'status',
    ];

    public function credential()
    {
        return $this->belongsTo(Credential::class, 'email', 'email');
    }

    public function updatedBy()
    {
        return $this->belongsTo(PersonalInformation::class, 'updated_by_id');
    }
}

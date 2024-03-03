<?php

namespace App\Models\Approvals;

use App\Models\PersonalInformation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
    use HasFactory;
    protected $fillable = [
        'approval_code',
        'name',
        'created_by_id',
        'updated_by_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(PersonalInformation::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(PersonalInformation::class, 'updated_by_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['report_request_id', 'file_path', 'file_name', 'file_type', 'generated_at'])]
class Report extends Model
{
    // Define the casts for the model attributes
    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    // Define the relationship with the ReportRequest model
    public function reportRequest()
    {
        return $this->belongsTo(ReportRequest::class, 'report_request_id');
    }
}

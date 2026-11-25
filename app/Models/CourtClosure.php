<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourtClosure extends Model
{
    protected $fillable = ['court_id', 'closure_date', 'reason'];

    protected function casts(): array
    {
        return ['closure_date' => 'date'];
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }
}

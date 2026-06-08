<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    // Relationship to the user who uploaded the file
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

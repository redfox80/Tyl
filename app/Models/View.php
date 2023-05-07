<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'view_contents', 'view_id', 'batch_id');
    }
}

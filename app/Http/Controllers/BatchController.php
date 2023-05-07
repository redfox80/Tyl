<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function getBatches()
    {
        return Inertia::render('Data/Batches')->with([
            'batches' => Batch::with('product')->get()
        ]);
    }
}

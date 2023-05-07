<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\View;
use App\Models\ViewContent;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class PalleController extends Controller
{
    public function palleDash(): Response
    {
        return Inertia::render('Paller/Paller', [
            'views' => View::all()
        ]);
    }

    public function postView(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:1'
        ]);

        $view       = new View;
        $view->name = $request->input('name');
        $view->save();

        return to_route('paller.view', $view->id);
    }

    public function getView(Request $request, $id): Response
    {
        $view = View::with(['batches', 'batches.product'])->findOrFail($id);

        return Inertia::render('Paller/Visning', [
            'view' => $view
        ]);
    }

    public function deleteView($id): RedirectResponse
    {
        $view = View::with('batches')->findOrFail($id);

        $relatedBatches = ViewContent::where('view_id', $view->id)->get();

        foreach ($relatedBatches as $rb) {
            $rb->delete();
        }

        $view->delete();
        return to_route('paller.dashboard');
    }

    public function postBatch(Request $request): RedirectResponse
    {
        $request->validate([
            'batchNumber' => 'required|gt:0',
            'viewId'      => 'required',
            'quantity'    => 'required'
        ]);

        $batch = Batch::where('number', $request->input('batchNumber'))->first();

        if ($batch == null) {
            $product = Product::where('name', $request->input('productType'))->first();
            if ($product == null){
                $product = new Product;
                $product->name = $request->input('productType');
                $product->save();
            }

            $batch              = new Batch;
            $batch->number      = $request->input('batchNumber');
            $batch->product_id  = $product->id;
            $batch->quantity    = $request->input('quantity');
            $batch->pallet_size = $request->input('palletSize');
            $batch->description = $request->input('description');
            $batch->save();
        } else {
            $vr = ViewContent::where('view_id', $request->input('viewId'))->where('batch_id', $batch->id)->first();
            if ($vr !== null) return redirect()->back()->withErrors([
                'Batch number is allready added to this view'
            ]);
        }

        //View Relationship to batch
        $vr           = new ViewContent;
        $vr->view_id  = $request->input('viewId');
        $vr->batch_id = $batch->id;
        $vr->save();

        return redirect()->back();
    }

    public function postSearch(Request $request)
    {
        $results = Batch::where('number', 'LIKE', $request->input('search') . '%')->limit(10)->get();

        return ([
            'search_results' => $results
        ]);
    }

    public function postRemoveFromView(Request $request)
    {

    }
}

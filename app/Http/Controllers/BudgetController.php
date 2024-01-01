<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetDetail;
use App\Models\User;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $budgets = Budget::with('details')->where('user_id', $request->userId)->get();
        
        return response()->json(["data"=> $budgets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name"           => ['required'],
            "amount"         => ['required'],
            "expride_date"   => ['required', 'date_format:Y-m-d'],
            "details.*.name" => ['required']
        ]);

        $budgetData = new Budget();
        $budgetData->name          = $validated['name'];
        $budgetData->amount        = $validated['amount'];
        $budgetData->expride_date  = $validated['expride_date'];
        $user = User::find($request->user_id);
        $budgetData->user()->associate($user);

        $budgetDetailData = new BudgetDetail();
        $budgetDetailData->details = $validated['details'];
        $budgetDetailData->details()->saveMany($budgetDetailData);
        dd($validated["details"]);
        $budgetData->save();

        return response()->json([], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Budget::destroy($id);

        return response()->json();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BudgetDetail;
use App\Models\Debt;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $userId)
    {
        $transactions = Transaction::where("user_id", $userId);
        
        return response()->json(["data"=> $transactions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name"           => ['required'],
            "amount"         => ['required'],
            "description"    => ['required'],
            "receipt"        => ['nullable']
        ]);
        //belum validasi file

        $transaction = new Transaction();

        $transaction->name          = $validated['name'];
        $transaction->amount        = $validated['amount'];
        $transaction->description   = $validated['description'];
        // $transaction->receipt = $validated['receipt'];

        $user = User::find($request->user_id);
        $wallet = Wallet::find($request->wallet_id);

        $transaction->user()->associate($user);
        $transaction->user()->associate($wallet);

        if ($request-> purposableType){
            if ($request->purposableType == 'debt'){
                $purposable = Debt::find($request->purposableId);
                
                //menambahkan amount_paid dna ubah status jika sudah lunas
            } else{
                $purposable = BudgetDetail::find($request->purposableId);
            }

            $transaction->purposable()->associate($purposable);
    
        }

        $transaction->save();
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
        // Budget::destroy($id);

        // return response()->json();
    }
}


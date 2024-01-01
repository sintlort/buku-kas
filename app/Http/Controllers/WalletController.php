<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\User;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $wallets = Wallet::where('user_id', $request->userId)->get();
        
        return response()->json(["data"=> $wallets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name"           => ['required'],
            "total_amount"   => ['required']
        ]);

        $wallet = new Wallet();
        $wallet->name          = $validated['name'];
        $wallet->total_amount  = $validated['total_amount'];

        $user = User::find($request->user_id);
        $wallet->user()->associate($user);
        $wallet->save();

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Wallet::destroy($id);

        return response()->json();
    }
}

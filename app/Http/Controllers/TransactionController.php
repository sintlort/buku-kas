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
     * Validating files can be seen on https://laravel.com/docs/10.x/validation#validating-files
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
        // Validasi file dapat dilakukan dengan cara enkripsi terlebih dahulu file nya di android menggunakan base32/base64
        // selanjutnya validasi di laravel menggunakal rules required
        // lalu di dekripsi file tersebut menggunakan base32/base64 dan di cek apakah data tersebut sebuah file atau tidak
        // pengecekan sebuah file pada dilakukan menggunakan metode ini https://stackoverflow.com/questions/62329321/how-can-i-check-a-base64-string-is-a-filewhat-type-or-not
        // mendapatkan 3/4 karakter pertama pada sebuah teks dapat menggunakan metode https://techvblogs.com/blog/get-first-character-from-string-php
        // jangan lupa validasi file tersebut di android juga
        

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
     * 
     */
    public function show(Request $request)
    {
        // Display spesifik data menggunakan $request
        // jika id transaksi diletakan pada link, maka perlu string $id pada fungsi update di controller contoh : https;//yourwebsite.com/update/25
        // jika id transaksi menjadi payload post, maka id transaksi berada pada $request
        // lebih dinamis jika id transaksi dijadikan payload, sehingga tidak perlu mengubah link di android setiap beda id.
        // Contoh kali ini menggunkan id transaksi yang terdapat pada payload post
        
        // Validasi dahulu data yang dikirimkan ke server agar tidak terjadi kasalahan dalam pencarian data, sbg contoh dikirimkan sebuah string, bukan numeric
        // maka akan terjadi eror 500, dimana sql tidak dapat mencari id dengan menggunakan string
        $validated = $request->validate([
            "id_transaksi"           => ['required','numeric'],
        ]);
        
        //Selanjutnya di ambil data dengan menggunakan where dan with, 'where' yaitu pencarian data dengan spesifik variable yang diinginkan
        // 'with' yaitu mengambil data relasi yang terhubung dengan data tersebut
        
        $dataTransaksi = Transaction::with('nama_relasi')->where('id_transaksi',$request->id_transaksi)->get();
        // Note : data relasi Belongs to dan Has Many itu berbeda, bisa dilakukan percobaan untuk relasi tersebut
        // Data relasi belongs to hanya terdapat satu data
        // Data relasi Has Many terdapat banyak data, sehingga return yang diberikan yaitu sebuah array, walaupun data yang terisi adalah 1, diperlukan looping untuk membaca data tersbut atau menggunakan indexing
        
        // Melakukan validasi apakah terdapat data atau tidak
        
        if(!empty($dataTransaksi)){
            //jika $dataTransaksi tidak kosong/empty
            //jalankan fungsi yang diinginkan atau return
            return response()->json(['status'=>'200','message'=>'none','data'=>$dataTransaksi],200);
        } else {
            // jika $dataTransaksi kosong/empty
            //jalankan fungsi yang diinginkan atau return
            return response()->json(['status'=>'404','message'=>'not found','data'=>''],200);
            //kode return/status bisa di sesuaikan dengan standar yang digunakan
        }
    }

    /**
     * Update the specified resource in storage.
     * Validation rules can be seen on https://laravel.com/docs/10.x/validation#available-validation-rules
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            "id_transaksi"           => ['required','numeric'],
            "input_name_yang_diinginkan" => ['required','rules yang diinginkan'],
        ]);
        // Rules dapat dilihat pada https://laravel.com/docs/10.x/validation#available-validation-rules
        // jika id transaksi diletakan pada link post update, maka perlu string $id pada fungsi update di controller
        // jika id transaksi menjadi payload post, maka id transaksi berada pada $request
        // lebih dinamis jika id transaksi dijadikan payload, sehingga tidak perlu mengubah link di android setiap beda id.
        
        // --Mengambil data
        // Ambil dulu data yang mau diambil
        $dataTransaksi = Transaction::find($request->id_transaksi);
        // Data sudah diambil, sekarang bisa di update sesuai keiinginan
        $dataTransaksi->nama_variable_di_table_nya = $request->input_name_yang_terdapat_pada_html_atau_blade;
        // Melakukan save pada database dapat dilakukan dengan menggunakan fungsi save();
        $dataTransaksi->save();
        // Setelah update, jangan lupa return ke page atau response json untuk android
        return response()->json(['status'=>'sukses','message'=>'none', 'data'=>$dataTransaksi],200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // Hapus spesifik data menggunakan $request
        // jika id transaksi diletakan pada link, maka perlu string $id pada fungsi update di controller contoh : https;//yourwebsite.com/delete/25
        // jika id transaksi menjadi payload post, maka id transaksi berada pada $request
        // lebih dinamis jika id transaksi dijadikan payload, sehingga tidak perlu mengubah link di android setiap beda id.
        // Contoh kali ini menggunkan id transaksi yang terdapat pada payload post
        
        // Validasi dahulu data yang dikirimkan ke server agar tidak terjadi kasalahan dalam pencarian data, sbg contoh dikirimkan sebuah string, bukan numeric
        // maka akan terjadi eror 500, dimana sql tidak dapat mencari id dengan menggunakan string
        $validated = $request->validate([
            "id_transaksi"           => ['required','numeric'],
        ]);
        
        // Mencari data yang diinginkan dengan menggunakan find()
        $dataTransaksiDestroy = Transaction::find($request->id_transaksi);
        // Hapus menggunakan delete();
        // Terdapat beberapa cara untuk melakukan delete, antara pakai delet() atau destroy()
        
        $dataTransaksiDestroy->delete();
        return response()->json(['status'=>'sukses','message'=>'none', 'data'=>''],200);
    }
}


<?php

namespace App\Http\Controllers;
use Auth;
use App\Saldo;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class SaldoController extends Controller
{
    //
    public function index(){
        $saldo= Saldo::all();
        //$data=['saldo'=>$saldo];
        return $data;
    }

    public function create(Request $request){
        try {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'],404);
            }
            $user = User::where('id', $user['id'])->first();
            if($request->jenis_transaksi=='kredit'){
                if($user->jml_saldo < $request->input('jumlah')){
                    return response()->json(['saldo kurang'], 404);
                }

            } else if($request->jenis_transaksi!='debit'){
                return response()->json(['error' => 'jenis_salah'], 400);

            }
        $saldo=new Saldo();
        $saldo->username=$user['username'];
        $saldo->jenis_transaksi=$request->input('jenis_transaksi');
        $saldo->nama_transaksi=$request->input('nama_transaksi');
        $saldo->jumlah=$request->input('jumlah');
        $saldo->save();
        if($request->jenis_transaksi=='debit'){
            $user->jml_saldo =$user->jml_saldo + $request ->input('jumlah');
        } else {
            $user->jml_saldo =$user->jml_saldo - $request ->input('jumlah');
        } 
        $user->save();
        return response()->json(compact('saldo','user'));
        } catch (\Exception $e){
                return response()->json([
                    'status' => '0', 'message' => 'gagal'
                ]);
        }
    }

    public function update(Request $request){

        try {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'],404);
            }
            
        $user = User::where('id',$user['id'])->first();
        $saldo = new Saldo();
        //$saldo=Saldo::find($user['id']);
        $saldo->username=$user->username;
        $saldo->jenis_transaksi=$request->input('jenis_transaksi');
        $saldo->nama_transaksi=$request->input('nama_transaksi');
        $saldo->jumlah=$request->input('jumlah');
      
    //    $user = JWTAuth::parseToken()->authenticate();

    //     $akun = User::where('id', $user->id);
    //     $akun->saldo=$akun->saldo=$request->jumlah;
        $saldo->save();
        $user->jml_saldo =$user->jml_saldo + $request->input('jumlah');
        $user->save();
        return response()->json(compact('saldo','user'));
        } catch (\Exception $e){
            return response()->json([
                'status' => '0', 'message' => 'gagal'
            ]);

               
    }
}
}
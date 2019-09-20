<?php

namespace App\Http\Controllers;

use App\User; //tambahkan ini
use Illuminate\Http\Request;
//tambahkan ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    //tambahkan ini
	public function login(Request $request)
	{
		$scredentials = $request->only('username', 'password');
		try {
			if (! $token = JWTAuth::attempt($scredentials)) {
				return response()->json(['error' => 'invalid_credentials'], 400);
			}
		} catch (JWTException $e) {
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		return response()->json(compact('token'));
	}

	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'jml_saldo' => 'required|integer',
		]);
		if($validator->fails()){
			return response()->json($validator->errors()->toJson(),400);
		}
		$user = User::create([
			'username' => $request->get('username'),
			'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'jml_saldo' => $request->get('jml_saldo'),
		]);
		$token = JWTAuth::fromUser($user);
		return response()->json(compact('user', 'token'), 201);
	}
	public function getAuthenticatedUser(Request $request)
	{
		try {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'],404);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			return response()->json(['token_expired'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['token_invalid'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			return response()->json(['token_absent'], $e->getStatusCode());
        }
        
        $id = $user['id'];
        $edit= User::where('id', $id)->first();
        $edit->jml_saldo =  $edit->jml_saldo + $request->input('jml_saldo');
        $edit->save();
        return $edit;
		return response()->json(compact('user'));
    
    
    }

}
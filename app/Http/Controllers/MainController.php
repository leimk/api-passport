<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name'                  =>  'required|string',
            'email'                 =>  'required|string|email||unique:users',
            'password'              =>  'required|string|confirmed'            
        ]);

        $user = new User([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  =>  bcrypt($request->password)
            ]);

        $user->save();

        return response()->json([
            'message'   =>  'User berhasil diregister.'            
        ],201);
    }


    public function login(Request $request)
    {
        $request->validate([            
            'email'                 =>  'required|string|email',
            'remember_me'           =>  'boolean'            
        ]);

        $userCredentials = request(['email','password']);

        if (!Auth::attempt($userCredentials))
            return response()->json([
                'message'   =>  'UnAuthorized'
            ],401);
        
        $user = $request->user();
        $tokenResult = $user->createToken('User Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now('Asia/Jakarta')->addWeeks(2);
        }else{
            $token->expires_at =Carbon::now('Asia/Jakarta')->addHours(1);
        }

        $token->save();

        return response()->json([
            'access_token'  =>  $tokenResult->accessToken,
            'token_type'    =>  'Bearer',
            'expires_at'    =>  Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);
        
    }


    public function logout(Request $request)
    {
        // if (Auth::check()) {
        //     Auth::user()->AauthAcessToken()->delete();
        //  }
        // dd($request);
        // $user = Auth::user()->token();
        
        // $user->revoke();
        $user = $request->user();
        dd($request);
        $request->user()->token()->revoke();
        return response()->json([
            'message'   =>  'Terima Kasih telah bertransaksi dengan Kami.Sampai Jumpa di transaksi berikutnya'
        ],200);
    }

    

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request)
    {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );
        $validator = Validator::make( $request->all(), $rules);

        if ( $validator->fails() )
            {
                return [
                    'status' => 404,
                    'message' => $validator->errors()->first()
                ];
            }
        $user = User::where('email', $request->email)->first();
        if ($user&&$user->id_syncro!=null) {
            if (Hash::check($request->get('password'), $user->password)) {
                    return response()->json(
                        ['data'=>$user->id_syncro, 'status'=>200,'message'=>'Success Retrieve Id Syncro']
                    );
            }else{
                return response()->json(
                    ['status'=>404,'message'=>'Password Not Match']
                );    
            }
        }if ($user&&$user->id_syncro==null) {
            return response()->json(
                ['status'=>404,'message'=>'Account Not Found']
            );    
        }
        else{
            return response()->json(
                ['status'=>404,'message'=>'User Not Found']
            );    
        }
    }

    public function register(Request $request)
    {
        $rules = array(
            'email' => 'email',
            'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:8'
        );
        $validator = Validator::make( $request->all(), $rules);

        if ( $validator->fails() )
            {
                return [
                    'status' => 404,
                    'message' => $validator->errors()->first()
                ];
            }
        $checkEmail=DB::table('users')->select('id')->where('email',$request->email)->first();
        if($checkEmail){
            return response()->json(
                ['data'=>$checkEmail, 'status'=>200,'message'=>'Success Retrieve Id']
            );
        }else{
            $user=new User();
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->save();
            return response()->json(
                ['data'=>$user->id, 'status'=>200,'message'=>'Success Retrieve Id']
            );
        }
    }

    public function updateIdSyncro(Request $request)
    {
        $user=User::where('email',$request->email)->first();
        if($user){
            $user=new User();
            $user=User::where('email',$request->email)->first();
            $user->id_syncro=$request->id_syncro;
            $user->save();
            return response()->json(
                ['data'=>$user->id_syncro, 'status'=>200,'message'=>'Success Retrieve Id']
            );
        }else{
            return response()->json(
                ['status'=>404,'message'=>'User Not Found']
            );
        }
        
    }
}

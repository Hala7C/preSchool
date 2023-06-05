<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\AUTH;
use App\Models\User;
use Laravel\Fortify\Rules\Password;
use Laravel\Jetstream\Jetstream;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;


class AuthController extends Controller
{

    /////////////////////////////////////////Register

    // public function register(Request $request)
    // {


    //     $validator = Validator::make($request->all(), [
    //         'name' => ['required', 'string', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', (new Password)->length(10)->requireNumeric()],
    //         'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
    //         'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }


    //     $user = User::create([
    //         'name' => $request->name,
    //         'role' =>'user',
    //         'password' => Hash::make($request->password),

    //     ]);
    //     if (isset($request->photo)) {
    //         $user->updateProfilePhoto($request->photo);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()
    //         ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 201);
    // }


    /////////////////////////////////////////login



    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('name', 'password'))) {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('name', $request['name'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        // return response()
        // ->json(['message' => 'Hi ' . $user->name . ', welcome to home', 'access_token' => $token, 'token_type' => 'Bearer', 'user' => $user], 200);
        $data=collect();
        $data->push([
            'user'=>$user,
            'access_token'=>$token,
            'token_type'=>'Bearer'
        ]);
        return ['data'=>$data,'status'=>201];
        // return response()
        //     ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 201);
    }



    /////////////////////////////////////////logout




    public function logout(User $user)
    {
        $user->tokens()->delete();

        // return response()
        //     ->json(['message' => 'You have successfully logged out and the token was successfully deleted'], 200);
            return ['data'=>'You have successfully logged out and the token was successfully deleted','status'=>200];
    }


    /////////////////////////////////////////updateProfile

    public function updateProfile(Request $request, $id)
    {


        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [

            'name'  => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'current_password' => ['sometimes','required', 'string'],
            'password' => ['sometimes','required', 'string', (new Password)->length(10)->requireNumeric()],
        ])->after(function ($validator) use ($user, $request) {
            if($request->password!=null){
            if (!isset($request->current_password) || !Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }}
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $path=null;
        if($request->hasFile('photo')){
            $photo =  $request->file("photo");
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('users/img', $newphoto);
            $path= 'users/img/' . $newphoto;
            $user->profile_photo_path=$path;
            $user->save();
        }
        if (isset($request->password)) {
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();
            if (request()->hasSession()) {
                request()->session()->put([
                    'password_hash_' . Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
                ]);
            }
        }
        $user->forceFill([
            'name' =>  $request->name,
        ])->save();

        // return response()
        //     ->json(['message' => 'You have successfully update '], 200);

            return['data'=>'You have successfully update','status'=>200];
    }
    /////////////////////////////////////////updatepassword



    public function updatepassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', (new Password)->length(10)->requireNumeric(), 'confirmed'],
        ])->after(function ($validator) use ($user, $request) {
            if (!isset($request->current_password) || !Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();
        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
            ]);
        }
        return response()
            ->json(['message' => 'password successfully update '], 200);
    }

    /////////////////////////////////////////profile



    public function profile()
    {
        // return  auth()->user();

        $user=User::findOrFail(Auth::user()->id);
        $student=$user->ownerable;
        if($user->role=='user'){
            $cid=$student->classs()->get();
             // $class=Classe::findOrFail($cid);
        if(count($cid)==0){
            $id=null;
        }else{
            $id=$cid[0]->id;
        }
        $data=collect();
        $data=([
            "id"=> $user->id,
            "name"=> $user->name,
            "email_verified_at"=>$user->name ,
            "two_factor_confirmed_at"=>$user-> email_verified_at,
            "current_team_id"=>$user-> current_team_id,
            "profile_photo_path"=> $user->profile_photo_path,
            "role"=> $user->role,
            "status"=> $user->status,
            "ownerable_type"=> $user->ownerable_type,
            "ownerable_id"=> $user->ownerable_id,
            "created_at"=> $user->created_at,
            "updated_at"=> $user->updated_at,
            "profile_photo_url"=>$user->profile_photo_url,
            "class_id"=>$id
        ]);
        }else{
            $data=$user;
        }

        return ['data'=>$data,'status'=>200];
    }
}

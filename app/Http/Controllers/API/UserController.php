<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return ['data' => $users, 'status' => '210'];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => ['required', 'alpha', 'max:255'],
            'password' => ['required', 'string', (new Password)->length(10)->requireNumeric(), 'confirmed'],
            'role' => ['required', 'in:teacher,manager,employee,bus_supervisor,admin'],
            'status' => ['required', 'in:active,suspended'],
            'ownerable_id' => ['required'],
            'ownerable_type' => ['required', 'in:employee,student']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input = [
            'name' => $request->name,
            'password' => $request->password,
            'role' => $request->role,
            'status' => $request->status,
            'ownerable_id' => $request->ownerable_id,
            'ownerable_type' => $request->ownerable_type,
        ];

        $user = User::create($input);

        return ['data' => $user, 'status' => 210];
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'alpha', 'max:255'],
            'role' => ['sometimes', 'required', 'in:teacher,manager,employee,bus_supervisor,admin'],
            'status' => ['sometimes', 'required', 'in:active,suspended']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (isset($request->password)) {
            $result = (new AuthController)->updatepassword($request, $id);
        }
        $user->update($request->all());
        // $user->name=$request->name;
        // $user->role=$request->role;
        // $user->status=$request->status;
        // $user->save();
        return ['data' => $user, 'status' => 210];
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'suspended';
        $user->save();
        return ['data' => 'user suspended successfully', 'status' => 210];
    }
}

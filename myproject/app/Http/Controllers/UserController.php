<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    //Dang ky nguoi dung
    public function create( Request $request)
    {
        $data = $request->validate([
            'username' => 'required|unique:users|string',
            'password' => 'required|string|size:8',
            'role_id' => 'required|integer',
            'email' => 'required|email',
        ],
        [
            'username.required' => 'Username is not null',
            'username.unique' => 'Username is already exist',
            'username.string' => 'Username is invalid',
            'password.required' => 'Password is not null',
            'password.string' => 'Password is invalid',
            'password.size' => 'Password is invalid',
            'role_id.required' => 'UserRole Id is not null',
            'role_id.integer' => 'UserRole Id is invalid',
            'email.required' => 'Email is not null',
            'email.email' => 'Email is invalid',
        ]);

        try {
            $user = User::create([
                'username' => $data['username'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
                'email' => $data['email'],
                'money_spent' => 0,
                'money_received' => 0
            ]);

            return response()->json([
                'message' => 'Add user success',
                'data' => new UserResource($user),
                'success' => true
            ], 201);
        }catch (Exception $e){
            return response()->json($e->getMessage(), 500);
        }

    }

    //Xem thong tin nguoi dung
    public function show(int $id)
    {
        try {
            User::findOrFail($id);
            $user = User::where('id', $id)->get();
            $userRole_id = User::where('id', $id)->value('role_id');
            if($userRole_id == 2 || $userRole_id == 3) {
                return response()->json([
                    'message' => 'Find user success',
                    'data' => UserResource::collection($user),
                    'success' => true
                ], 200);
            }
            if($userRole_id == 1){
                return response()->json([
                    'message' => 'Find user success',
                    'data' => AdminResource::collection($user),
                    'success' => true
                ], 200);
            }
        }catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }else {
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    //Thay doi thong tin nguoi dung
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'username' => 'required|unique:users|string',
            'email' => 'required|email',
        ], [
            'username.required' => 'Username is not null',
            'username.unique' => 'Username is already exist',
            'username.string' => 'Username is invalid',
            'email.required' => 'Email is not null',
            'email.email' => 'Email is invalid',
        ]);

        try{
            User::findOrFail($id);
            $userRole_id = User::where('id', $id)->value('role_id');
            if($userRole_id != 2 && $userRole_id != 3) {
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }
            User::findOrFail($id)->update([
                'username' => $data['username'],
                'email' => $data['email']
            ]);
            return response()->json([
                'message' => 'Update user success',
                'success' => true
            ], 201);

        } catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }else {
                return response()->json($e->getMessage(), 500);
            }
        }
    }

}

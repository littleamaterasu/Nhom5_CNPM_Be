<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartsResource;
use App\Http\Resources\GameFolderResource;
use App\Http\Resources\TransactionsResource;
use App\Models\Cart;
use App\Models\Game;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;

class CartController extends Controller
{
    //Them game vao gio hang
    public function create(int $id ,Request $request)
    {
        try {
            User::findOrFail($id);
            $userRole_id = User::where('id', $id)->value('role_id');
            if($userRole_id != 3) {
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }

            $data = $request->validate([
                'game_id' => 'required|integer|',
            ],[
                'game_id.required' => 'GameId is not null',
                'game_id.integer' => 'GameId is invalid'
            ]);

            $game_id = Game::where('id', $data['game_id'])->value('id');
            if($game_id == null){
                return response()->json([
                    'message' => 'Game does not exist',
                    'success' => false
                ], 404);
            }

            $check_game_exist = Cart::where('game_id', $data['game_id'])->value('game_id');
            $user_exist_id = Cart::where('game_id', $check_game_exist)->pluck('user_id')->toArray();
            if($check_game_exist != null){
                if(in_array($id, $user_exist_id)){
                    return response()->json([
                        'message' => 'Cannot add game',
                        'success' => false
                    ], 400);
                }
            }

            $cart = Cart::create([
                'user_id' => $id,
                'game_id' => $game_id,
                'state' => 0
            ]);
            return response()->json([
                'message' => 'Create cart success',
                'data' => new CartsResource($cart),
                'success' => true
            ], 200);

        }catch ( Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            } else{
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    //Mua tro choi va tao giao dich
    public function update(int $id, Request $request)
    {
        try {
            $data = $request->validate([
                'game_id' => 'required|integer',
                'transaction_bank' => 'required|string'
            ],[
                'game_id.required' => 'GameId is not null',
                'game_id.integer' => 'GameId is invalid',
                'transaction_bank.required' => 'Transaction Bank is not null',
                'transaction_bank.string' => 'Transaction Bank is invalid'
            ]);

            User::findOrFail($id);
            //Mua tro choi
            $cart_id = Cart::where('game_id', $data['game_id'])
                ->where('user_id', $id)
                ->value('id');

            if($cart_id == null){
                return response()->json([
                    'message' => 'User or game not found',
                    'success' => false
                ], 404);
            } else{
                Cart::where('id', $cart_id)->update([
                'state' => 1
                ]);
            }

            //Tao giao dich
            $game_id = Game::where('id', $data['game_id'])->value('id');

            $check_game_exist = Transaction::where('game_id', $data['game_id'])->value('game_id');
            $user_exist_id = Transaction::where('game_id', $check_game_exist)->pluck('user_id')->toArray();
            if($check_game_exist != null){
                if(in_array($id, $user_exist_id)){
                    return response()->json([
                        'message' => 'Game was previously purchased',
                        'success' => false
                    ], 400);
                }
            }

            $amount = Game::where('id', $data['game_id'])->value('price');
            $transaction_amount = strval($amount);

            $transaction = Transaction::create([
                'user_id' => $id,
                'game_id' => $game_id,
                'transaction_amount' => $transaction_amount,
                'transaction_bank' => $data['transaction_bank']
            ]);

            //Cap nhat tien:
            $money = Game::where('id', $game_id)->value('price');
            $publisher_id = Game::where('id', $game_id)->value('user_id');
            $old_money_spent_user = User::where('id', $id)->value('money_spent');
            $old_money_received_publisher = User::where('id', $publisher_id)->value('money_received');
            $new_money_spent_user = $money + $old_money_spent_user;
            $new_money_received_publisher = $money + $old_money_received_publisher;

            User::where('id', $id)->update([
                'money_spent' => $new_money_spent_user
            ]);

            User::where('id', $publisher_id)->update([
                'money_received' => $new_money_received_publisher
            ]);

            return response()->json([
                'message' => 'Create transaction success',
                'data' => new TransactionsResource($transaction),
                'success' => true
            ], 201);


        }catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            } else{
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    //Xem gio hang
    public function show(int $id)
    {
        try {
            User::findOrFail($id);
            $cart = Cart::where('user_id', $id)
                ->where('state', 0)
                ->get();
            $cart_count = Cart::where('user_id', $id)
                ->where('state', 0)
                ->pluck('id')->toArray();
            if(count($cart_count) == 0){
                return response()->json([
                    'message' => 'Cart not found',
                    'success' => false
                ], 404);
            }
            return response()->json([
                'message' => 'Find success',
                'data' => CartsResource::collection($cart),
                'success' => true
            ], 200);
        } catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            } else{
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    public function index(int $id)
    {
        try {
            User::findOrFail($id);
            $cart = Cart::where('user_id', $id)
                ->where('state', 1)
                ->get();

            $cart_count = Cart::where('user_id', $id)
                ->where('state', 1)
                ->pluck('id')->toArray();
            if(count($cart_count) == 0){
                return response()->json([
                    'message' => 'Cart not found',
                    'success' => false
                ], 404);
            }
            return response()->json([
                'message' => 'Find success',
                'data' => CartsResource::collection($cart),
                'success' => true
            ]);
        } catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ]);
            } else{
                return response()->json($e->getMessage());
            }
        }
    }

}

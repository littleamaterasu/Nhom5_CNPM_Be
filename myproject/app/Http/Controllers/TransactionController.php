<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionsResource;
use App\Models\Cart;
use App\Models\Game;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //Nguoi mua xem giao dich
    public function show(int $id)
    {
        try {
            User::findOrFail($id);
            $transaction = Transaction::where('user_id', $id)->get();
            return response()->json([
                'message' => 'Find success',
                'data' => TransactionsResource::collection($transaction),
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

    //Nguoi ban xem giao dich cua moi game
    public function index(int $id, Request $request)
    {
        try {
            User::findOrFail($id);

            $role = User::where('id', $id)->value('role_id');
            if($role != 2){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }

            $data = $request->validate([
                'game_id' => 'required|integer',
            ],[
                'game_id.required' => 'GameId is not null',
                'game_id.integer' => 'GameId is invalid',
            ]);

            $game_id = Game::where('id', $data['game_id'])->value('id');
            $user_id = Game::where('id', $game_id)->value('user_id');
            if($user_id != $id){
                return response()->json([
                    'message' => 'Game is not found',
                    'success' => false
                ], 404);
            }

            $transaction = Transaction::where('game_id', $game_id)->get();
            $transactions = Transaction::where('game_id', $game_id)->pluck('id')->toArray();
            if(count($transactions) == 0){
                return response()->json([
                    'message' => 'Game has no transaction',
                    'success' => false
                ],200);
            }else{
                return response()->json([
                    'message' => 'Find transaction success',
                    'data' => TransactionsResource::collection($transaction),
                    'success' => true
                ],200);
            }

        } catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ],404);
            } else{
                return response()->json($e->getMessage(),500);
            }
        }
    }

    //Nguoi ban xem doanh thu
    public function revenue(int $id, Request $request)
    {
        try {
            User::findOrFail($id);

            $role = User::where('id', $id)->value('role_id');
            if($role != 2){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ], 404);
            }

            $data = $request->validate([
                'game_id' => 'required|integer',
            ],[
                'game_id.required' => 'GameId is not null',
                'game_id.integer' => 'GameId is invalid',
            ]);

            $game_id = Game::where('id', $data['game_id'])->value('id');
            $user_id = Game::where('id', $game_id)->value('user_id');
            if($user_id != $id){
                return response()->json([
                    'message' => 'Game is not found',
                    'success' => false
                ], 404);
            }

            $game_sold = Cart::where('game_id', $game_id)
                ->where('state', 1)
                ->pluck('id')->toArray();
            $price = Game::where('id', $game_id)->value('price');
            $game_sold_count = count($game_sold);
            $result = $game_sold_count * $price;

            return response()->json([
                'message' => 'Revenue calculation completed',
                'data' => $result,
                'success' => true
            ],200);

        }catch (Exception $e){
            if($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'User is not found',
                    'success' => false
                ],404);
            } else{
                return response()->json($e->getMessage(),500);
            }
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedbacksResource;
use App\Models\Cart;
use App\Models\Feedback;
use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;

class FeedbackController extends Controller
{
    //Tao bai danh gia
    public function create(int $id, Request $request)
    {
        try {
            User::findOrFail($id);
            $data = $request->validate([
                'game_id' => 'required|integer',
                'content' => 'required|string'
            ],[
                'game_id.required' => 'GameId is not null',
                'game_id.integer' => 'GameId is invalid',
                'content.required' => 'Content is not null',
                'content.string' => 'Content is invalid'
            ]);

            $user_id = Cart::where('user_id', $id)->value('user_id');
            if($user_id == null){
                return response()->json([
                    'message' => 'User not found',
                    'success' => false
                ], 404);
            }

            $game_id = Game::where('id', $data['game_id'])->value('id');
            if($game_id == null){
                return response()->json([
                    'message' => 'Game does not exist',
                    'success' => false
                ], 404);
            }

            //Check trong feedback xem co binh luan chua
            $check_game_exist = Feedback::where('game_id', $data['game_id'])->value('game_id');
            $user_exist_id = Feedback::where('game_id', $check_game_exist)->pluck('user_id')->toArray();
            if($check_game_exist != null){
                if(in_array($id, $user_exist_id)){
                    return response()->json([
                        'message' => 'You have already commented on this game',
                        'success' => false
                    ], 400);
                }
            }

            //Check xem nguoi do mua hang chua
            $check_cart = Cart::where('user_id', $id)
                ->where('game_id', $game_id)
                ->where('state', 1)
                ->pluck('id')->toArray();

            if(count($check_cart) != 0){
                $feedback = Feedback::create([
                'game_id' => $game_id,
                'user_id' => $id,
                'content' => $data['content']
            ]);
            return response()->json([
                'message' => 'Create feedback success',
                'data' => new FeedbacksResource($feedback),
                'success' => true
            ],201);
            }else{
                return response()->json([
                    'message' => 'You need to experience the game before rating',
                    'success' => false
                ], 400);
            }

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

    //Nguoi ban hang xem bai danh gia game cua ban than
    public function show(int $id, Request $request)
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

            $feedback = Feedback::where('game_id', $game_id)->get();
            $feedbacks = Feedback::where('game_id', $game_id)->pluck('id')->toArray();
            if(count($feedbacks) == 0){
                return response()->json([
                    'message' => 'Game has no feedback',
                    'success' => false
                ], 404);
            }else{
                return response()->json([
                    'message' => 'Find feedback success',
                    'data' => FeedbacksResource::collection($feedback),
                    'success' => true
                ], 200);
            }

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

    //Xem tat ca danh gia cua 1 game
    public function getAllFeedback(int $id)
    {
        try {
            Game::findOrFail($id);
            $feedback = Feedback::where('game_id', $id)->get();
            $feedbacks = Feedback::where('game_id', $id)->pluck('id')->toArray();
            if(count($feedbacks) == 0){
                return response()->json([
                    'message' => 'Game has no feedback',
                    'success' => false
                ],200);
            } else{
                return response()->json([
                    'message' => 'Find feedback success',
                    'data' => FeedbacksResource::collection($feedback),
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

}

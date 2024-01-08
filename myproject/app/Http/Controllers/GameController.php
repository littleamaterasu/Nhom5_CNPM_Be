<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameFolderResource;
use App\Http\Resources\GamesResource;
use App\Models\Game;
use App\Models\GameGenre;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;

class GameController extends Controller
{

    //CreateGame--Them tro choi vao danh sach ban
    public function create(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'game_folder' => 'required|string',
            'price' => 'required|integer',
            'genre' => 'required|string'
        ],[
            'name.required' => 'Name is not null',
            'name.string' => 'Name is invalid',
            'name.unique' => 'Name is already exist',
            'image.image' => 'Image is invalid',
            'image.required' => 'Image is not null',
            'image.mimes' => 'Image is invalid',
            'image.max' => 'Image is invalid',
            'game_folder.required' => 'Game folder is not null',
            'game_folder.string' => 'Game folder is invalid',
            'price.required' => 'Price is not null',
            'price.integer' => 'Price is invalid',
            'genre.required' => 'Game genre is not null',
            'genre.string' => 'Game genre is invalid'
        ]);

        $genre = $data['genre'];
        $genre_id = GameGenre::where("name", $genre)->value('id');
        if($genre_id == null){
            return response()->json([
                'message' => 'Game genre is not found',
                'success' => false
            ], 404);
        }

        try {

            $user_id = User::findOrFail($id)->getKey();
            $userRole_id = User::where('id', $id)->value('role_id');
            if($userRole_id != 2) {
                return response()->json([
                    'message' => 'User account is invalid',
                    'success' => false
                ], 404);
            }

            $imagePath = $request->file('image')->store('game_images', 'public');

            $game = Game::create([
                'name' => $data['name'],
                'image' => $imagePath,
                'game_folder' => $data['game_folder'],
                'price' => $data['price'],
                'genre_id' => $genre_id,
                'user_id' => $user_id
            ]);
            return response()->json([
                'message' => 'Add game success',
                'data' => new GamesResource($game),
                'success' => true
            ], 201);

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

    //ShowGame--Tim kiem tro choi theo id nguoi ban
    public function show(int $id)
    {
        try {
            $user_id = User::findOrFail($id)->getKey();
            $userRole_id = User::where('id', $id)->value('role_id');
            if($userRole_id != 2) {
                return response()->json([
                    'message' => 'User account is invalid',
                    'success' => false
                ], 404);
            }

            $additionalData = User::where('id', $id)->value('username');
            $games = Game::where('user_id', $user_id)->get();

            return response()->json([
                'message' => 'Find game success',
                'data' => GamesResource::collection($games),
                'success' => true
            ], 200);

        } catch (Exception $e) {
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

    //Hien thi chi tiet link game
    public function link(int $id)
    {
        try {
            $game = Game::where('id', $id)->get();
            return response()->json([
                'message' => 'Find game success',
                'data' => GameFolderResource::collection($game),
                'success' => true
            ], 200);
        }catch (Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }

    //Hien thi 10 tro choi moi nhat
    public function index()
    {
        try {
            $games = Game::latest('created_at')->take(10)->get();
            return response()->json([
                'message' => 'Find game success',
                'data' => GamesResource::collection($games),
                'success' => true
            ]);
        } catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }

    //Tim kiem tro choi theo the loai
    public function findGame(Request $request)
    {
        try {
            $data = $request->validate([
                'genre' => 'required|string'
            ],[
                'genre.required' => 'Game genre is not null',
                'genre.string' => 'Game genre is invalid'
            ]);

            $genre_id = GameGenre::where('name', $data['genre'])->value('id');
            if($genre_id == null){
                return response()->json([
                    'message' => 'Game genre not found',
                    'success' => false
                ],404);
            }else{
                $games = Game::where('genre_id', $genre_id)->latest('created_at')->take(10)->get();
                return response()->json([
                    'message' => 'Find game success',
                    'data' => GamesResource::collection($games),
                    'success' => true
                ],200);
            }

        } catch (Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

}

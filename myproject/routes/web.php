<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Game routes
//Route::prefix('/publisher')->name('publisher.')->group(function () {
//    Route::post();
//});

//Them người dùng
Route::post('/user/register', [UserController::class, 'create'])->name('user.register');

//Xem thông tin người dùng:
Route::get('user/show/{id}', [UserController::class, 'show'])->name('user.show');

//Cập nhật thông tin người dùng:
Route::put('user/update/{id}', [UserController::class, 'update'])->name('user.update');

//Người dùng đăng game để bán
Route::post('/game/add/{id}', [GameController::class, 'create'])->name('game.create');

//Người dùng tìm kiếm tất cả trò chơi bản thân đăng bán
Route::get('game/show/{id}', [GameController::class, 'show'])->name('game.show');

//Người dùng thay đổi thông tin trò chơi

//Người dùng thêm trò chơi vào giỏ hàng
Route::post('cart/add/{id}', [CartController::class, 'create'])->name('cart.create');

//Người dùng mua trò chơi
Route::put('cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

//Người dùng xem giỏ hàng
Route::get('cart/show/{id}', [CartController::class, 'show'])->name('cart.show');

//Người dùng xem game đã mua
Route::get('cart/index/{id}', [CartController::class, 'index'])->name('cart.index');

//Người dùng xem chi tiết link game
Route::get('game/link/{id}', [GameController::class, 'link'])->name('link');

//Người dùng xem giao dịch
Route::get('transaction/show/{id}', [TransactionController::class, 'show'])->name('transaction.show');

//Người dùng tải lên bài đánh giá
Route::post('feedback/create/{id}', [FeedbackController::class, 'create'])->name('feedback.create');

//Người dùng xem đánh giá của từng trò chơi
Route::get('feedback/show/{id}', [FeedbackController::class, 'show'])->name('feedback.show');

//Người dùng xem giao dịch của mỗi trò chơi
Route::get('transaction/index/{id}', [TransactionController::class, 'index'])->name('transaction.index');

//Xem doanh thu cua tung game
Route::get('revenue/{id}', [TransactionController::class, 'revenue'])->name('revenue');

//Nhung chuc nang cho tat ca nguoi dung:

//Xem bình luận game:
Route::get('game/feedback/{id}', [FeedbackController::class, 'getAllFeedback'])->name('get-all-feedback');

//Hiển thị 10 game mới nhất
Route::get('game/index', [GameController::class, 'index'])->name('game.index');

//Tìm kiếm game theo thể loại
Route::get('game/find', [GameController::class, 'findGame'])->name('find-game');

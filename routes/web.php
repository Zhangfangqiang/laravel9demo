<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//用户登录注册
Auth::routes();

//邮箱验证界面
Route::get('/email/verify', function () {
  return view('auth.verify');
})->middleware('auth')->name('verification.notice');

//修改数据库字段界面
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();
  session()->flash('success','邮箱验证成功');
  return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

//发送邮件方法
Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();
  return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



Route::get('/', 'StaticPagesController@home')->middleware('auth')->middleware('verified');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about');

/**
 * GET	   /users	UsersController@index	            显示所有用户列表的页面
 * GET	   /users/{user}	UsersController@show	    显示用户个人信息的页面
 * GET	   /users/create	UsersController@create	  创建用户的页面
 * POST	   /users	UsersController@store           	创建用户
 * GET	   /users/{user}/edit	UsersController@edit	编辑用户个人资料的页面
 * PATCH	 /users/{user}	UsersController@update	  更新用户
 * DELETE	 /users/{user}	UsersController@destroy	  删除用户
 */
Route::resource('users'                 , 'UsersController');                                                                        //资源路由器
Route::get     ('/users/{user}/followings','UsersController@followings')->name('users.followings');
Route::get     ('/users/{user}/followers' ,'UsersController@followers')->name('users.followers');
Route::post    ('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete  ('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');
Route::resource('statuses'              , 'StatusesController', ['only' => ['store', 'destroy']])->middleware('auth');  //资源路由器

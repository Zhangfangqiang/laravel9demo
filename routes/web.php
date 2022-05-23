<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'StaticPagesController@home')->middleware('auth');
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
Route::resource('users', 'UsersController');        //资源路由器

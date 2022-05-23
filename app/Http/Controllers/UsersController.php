<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth', [
      'except' => ['show', 'create', 'store', 'index']
    ]);

    $this->middleware('guest', [
      'only' => ['create']
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::paginate(6);
    return view('users.index', compact('users'));
  }

  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    $statuses = $user->statuses()
      ->orderBy('created_at', 'desc')
      ->paginate(10);
    return view('users.show', compact('user','statuses'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    $this->authorize('update', $user);
    return view('users.edit', compact('user'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, User $user)
  {
    $this->authorize('update', $user);
    $this->validate($request, [
      'name' => 'required|max:50',
      'password' => 'required|confirmed|min:6'
    ]);

    $user->update([
      'name' => $request->name,
      'password' => bcrypt($request->password),
    ]);

    return redirect()->route('users.show', $user->id);
  }

  public function destroy(User $user)
  {
    $this->authorize('destroy', $user);
    $user->delete();
    session()->flash('success', '成功删除用户！');
    return back();
  }

  public function followings(User $user)
  {
    $users = $user->followings()->paginate(30);
    $title = $user->name . '关注的人';
    return view('users.show_follow', compact('users', 'title'));
  }

  public function followers(User $user)
  {
    $users = $user->followers()->paginate(30);
    $title = $user->name . '的粉丝';
    return view('users.show_follow', compact('users', 'title'));
  }
}

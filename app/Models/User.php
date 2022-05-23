<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * 一对多
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function statuses()
  {
    return $this->hasMany(Status::class);
  }

  /**
   * 多对多   我追随的用户
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function followers()
  {
    return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
  }

  /**
   * 多对多 追随我的用户
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function followings()
  {
    //目标表  关系表   外键
    return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
  }

  /**
   * 追随
   * @param $user_ids
   */
  public function follow($user_ids)
  {
    if ( ! is_array($user_ids)) {
      $user_ids = compact('user_ids');
    }
    $this->followings()->sync($user_ids, false);
  }

  /**
   * 取消追随
   * @param $user_ids
   */
  public function unfollow($user_ids)
  {
    if ( ! is_array($user_ids)) {
      $user_ids = compact('user_ids');
    }
    $this->followings()->detach($user_ids);
  }

  /**
   * 是否追随
   * @param $user_id
   * @return mixed
   */
  public function isFollowing($user_id)
  {
    return $this->followings->contains($user_id);
  }

  public function feed()
  {
    $user_ids = $this->followings->pluck('id')->toArray();
    array_push($user_ids, $this->id);
    return Status::whereIn('user_id', $user_ids)
      ->with('user')
      ->orderBy('created_at', 'desc');
  }

  /**
   * 在模型中定义一个方法
   * @param string $size
   * @return string
   */
  public function gravatar($size = '100')
  {
    $hash = md5(strtolower(trim($this->attributes['email'])));
    return "https://cdn.v2ex.com/gravatar/$hash?s=$size";
  }
}

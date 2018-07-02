<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    //単なるhasMany 1:Nの記述
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //N:Nの記述
    //followings が User がフォローしている User 達 user_id 自分、follow_id相手
    //第三引数に中間テーブルに保存されている自分の id を示すカラム名 (user_id) を指定し、
    //第四引数に中間テーブルに保存されている関係先の id を示すカラム名 (follow_id) を指定します。
    public function followings()
    {
        return $this->belongsToMany(User::class,'user_follow','user_id','follow_id')->withTimestamps();
        //return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
        //withTimestamps() は中間テーブルにも created_at と updated_at を保存するためのメソッドでタイムスタンプを管理することができるようになります。
    }
    
    //N:Nの記述
    //followers が User をフォローしている User 達です。follow_id自分 usr_id相手 
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    //followするメソッド（INSERT)
    public function follow($userId)
    {
         // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
        
    }

    //unfollowするメソッド（DELETE)
    //attach() と detach() というメソッドが用意されている
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    //TRUE/FALSEを返すメソッド（内部用）
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    //
    public function feed_microposts()
    {
        //User がフォローしている User の id の配列を取得
        //pluck() は与えられた引数のテーブルのカラム名だけを抜き出します。
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        
        //更に $follow_user_ids[] = $this->id; で自分の id も追加しています。自分自身のマイクロポストも表示させるためです。
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }

    //＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊Favoriteで追加

    //N:Nの記述
    public function favoritePost()
    {
        return $this->belongsToMany(Micropost::class, 'favorite', 'user_id', 'favorite_id')->withTimestamps();
    }    
    
    //FavoriteにInsertする。
    public function favorite($FId)
    {
        //自分のUser_IDを元に、既にFavoriteテーブルに存在するかチェック 
        $exist = $this->is_favorite($FId);

        if ($exist) {
            // 既にFavoriteしていれば何もしない
            return false;
        } else {
            // 未FavoriteであればFavoriteする
            $this->favoritePost()->attach($FId);
            return true;
        }
    }

    //FavoriteからDeleteする。
    public function unfavorite($FId)
    {
        // チェック
        $exist = $this->is_favorite($FId);

        if ($exist) {
        $this->favoritePost()->detach($FId);
            return true;
        } else {
            return false;
        }
    }
    
    //FIDを元に、favoritePostメソッドの返すオブジェクトを使って、存否確認
    //自分のUserIDを元に、引数のFID
    public function is_favorite($FId) {
        return $this->favoritePost()->where('favorite_id', $FId)->exists();
    }

    //Favorteのタイムライン用のマイクロポストを取得
    public function feed_Favorite_microposts()
    {
        //$follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $Favorite_ids = $this->favoritePost()-> pluck('microposts.id')->toArray();
        return Micropost::whereIn('id', $Favorite_ids);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
}

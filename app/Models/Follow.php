<?php
namespace App\Models;

class Follow extends Model {

    protected $table = 'follow';
   
    public function countFollow($follower_id){
        $follow = $this->query("SELECT COUNT(follow_id) AS count FROM follow WHERE follower_id  = ?", [$follower_id], true);
        if($follow)
            return $follow;
        else
            return $this;
    }

    public function countFollower($follow_id){
        $follower = $this->query("SELECT COUNT(follower_id) AS count FROM follow WHERE follow_id  = ?", [$follow_id], true);
        if($follower)
            return $follower;
        else
            return $this;
    }

    public function follow($follow_id, $follower_id){
        $follow = $this->query("INSERT INTO {$this->table}(follow_id, follower_id, follow_date) VALUES (?, ?,?)", [$follow_id, $follower_id, date('Y-m-d H:i:s')], true);
        if($follow)
            return $follow;
        else
            return $this;
    }

    public function unfollow($follow_id, $follower_id){
        $unfollow = $this->query("DELETE FROM {$this->table} WHERE follower_id = ? AND follow_id = ?", [$follower_id, $follow_id], true);
        if($unfollow)
            return $unfollow;
        else
            return $this;
    }

    public function getfollowers($follow_id){
        $usersFollowers = $this->query("SELECT follow_id,user.id AS 'follower_id',user.username,user.name,user.avatar FROM follow INNER JOIN user ON follower_id = user.id WHERE follow_id = ?", [$follow_id]);
        if($usersFollowers)
            return $usersFollowers;
        else
            return $this;
    }
    public function getfollowing($follower_id){
        $usersFollowing = $this->query("SELECT follower_id,user.id AS 'follow_id',user.username,user.name,user.avatar FROM follow INNER JOIN user ON follow_id = user.id WHERE follower_id = ?", [$follower_id]);
        if($usersFollowing)
            return $usersFollowing;
        else
            return $this;
    }
    
    public function getFollow($follow_id, $follower_id){
        $follow = $this->query("SELECT * FROM {$this->table} WHERE follow_id = ? AND follower_id = ?", [$follow_id, $follower_id], true);
        if($follow)
            return $follow;
        else
            return $this;
    }

    public function isFollowed($user_id ,$profile_id){
            // echo "user".$user_id."profile".$profile_id.'/n\n';
        $isFollow = $this->query("SELECT follow_id , follower_id, COUNT(follow_id) AS count FROM follow WHERE follow_id = ? and `follower_id` = ?", [$user_id, $profile_id]);
        if($isFollow[0]->count > 0)
            return "Following";
        elseif($isFollow[0]->count == 0)
            return "Follow";
        // return $isFollow;
        else
            return $this;
    }
    public function randomFollow($user_id){
		$whoToFollow = $this->query("SELECT * FROM `user` WHERE `id` != ? AND `id` NOT IN (SELECT `follower_id` FROM `follow` WHERE `follow_id` = ?) ORDER BY rand() LIMIT 3", [$user_id, $user_id]);
		if($whoToFollow)
            return $whoToFollow;
        else
            return $this;
	}
    public function FollowsYou($profile_id , $user_id){
		$FollowsYou = $this->query("SELECT * FROM follow WHERE follow_id = ? AND follower_id = ?", [$profile_id, $user_id]);
        if($FollowsYou)
            return $FollowsYou;
        else
            return $this;
	}

}
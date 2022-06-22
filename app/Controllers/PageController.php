<?php
namespace App\Controllers;

use App\Models\Follow;
use App\Models\User;
use App\Models\Hashtag;
use App\Models\Tweet;

class PageController extends Controller
{
    
    public function home()
    {
            $titrepage = 'Accueil - ';
            $js = 'SendTweet.js';
            $css = 'home.css';
            if(isset($_SESSION['user_id'])){
                $user_id = $_SESSION['user_id'];
                $randomFollow = (new Follow($this->getDB()))->randomFollow($user_id);
                return $this->view('pages.home',compact('titrepage','css','js','randomFollow'));
            }

           
            return $this->view('pages.home',compact('titrepage','css', 'js'));
    }

    public function display()
    {
        return $this->simpleView('pages.display');
    }

    public function galerie()
    {
        $titrepage = 'Galerie';
        return $this->simpleView('pages.galerie',compact('titrepage'));
    }

    public function SendTweet()
    {
       $id_user = $_SESSION['user_id'];
         $heure = date("Y-m-d h:m:s");
         $contenu = $_POST['tweet'];
        if(count($_POST) === 2){
            var_dump($_POST['tweet']);
            $tweet = (new Tweet($this->getDB()))->sendTweets($contenu,$heure,$id_user);
        }
        if(count($_POST) === 1){
            // var_dump($_POST);
            // var_dump($_FILES);
    // var_dump(SCRIPTS);
            $target_dir = "../public/assets/TweetImg/";
            $file = $_FILES['image']['name'];
            $path = pathinfo($file);
            $filename = "public/assets/TweetImg/". $file;
            $ext = $path['extension'];
            $temp_name = $_FILES['image']['tmp_name'];
             $path_filename_ext = $target_dir .$file;
            move_uploaded_file($temp_name, $path_filename_ext);
            $tweet = (new Tweet($this->getDB()))->sendTweetsImage($contenu, $filename, $heure,$id_user);
        }
            
        
    
    }

    public function displayTweets(){
        $id_user = $_SESSION['user_id'];
          $displayTweet = (new Tweet($this->getDB()))->GetTweets($id_user);
          echo json_encode($displayTweet);
    }


}
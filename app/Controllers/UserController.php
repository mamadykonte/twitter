<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Theme;
use App\Models\Tweet;
use App\Models\Follow;
use App\Controllers\Controller;

class UserController extends Controller
{
    
    public function followers($username)
    {
        $css= 'follow.css';
        $js = 'follow.js';
        $titrepage = 'Profile / Twitter';
        $usrnm = $username;
       
        $user_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $username = htmlspecialchars($username);
        $username = '@'.$username;

        $users = (new User($this->getDB()))->findByUsername($user_username);
        if($users->id){
            $user = $users;
            // var_dump($user);
        } 

        $profile = (new User($this->getDB()))->findByUsername($username);
        if(!empty($profile->id)){
            $titrepage = $profile->name.' ('.$profile->username.') / Twitter';
            $followers = (new Follow($this->getDB()))->getFollowers($profile->id);
            $following = (new Follow($this->getDB()))->getFollowing($profile->id);
            $isFollowed = (new Follow($this->getDB()))->isFollowed($profile->id,$user->id);
            return $this->view('pages.followers', compact('titrepage','css','js','profile','user','isFollowed','followers','following'));
        }
        else{
            return $this->view('pages.followers', compact('titrepage','css','js'));
        }

    }

    public function login()
    {
        return $this->simpleView('auth.login');
    }

    public function loginPost()
    {
        $_SESSION['errorLogin'] = '';  
           $email = htmlspecialchars($_POST['email']);
           $password = htmlspecialchars($_POST['password']);
           $user = (new User($this->getDB()))->findByEmail($email);
           $pass = hash('ripemd160', $password . 'vive le projet Tweet_academy');

            if (!empty($user->password)) {
                if (($user->password === $pass) && $user)  //hash le mot de passe
                {
                    $_SESSION['errorLogin'] = '';
                    $_SESSION['user_id'] = (int) $user->id;
                    $_SESSION['username'] = $user->username;
                    $_SESSION['name'] = $user->name;
                    $_SESSION['email'] = $user->email;
                    return header('Location: ' . BASE_NAME ); //  l'admin est connecter il sera rediriger vers la page profile
                }
                else{
                    var_dump($_SESSION);
                    $_SESSION['errorLogin'] = "La connexion a échoué, veuillez réessayer";
                }

            }
            else{
                return $this->simpleView('auth.login');
            }
        
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        return header('Location:' . BASE_NAME.'login');
    }

    public function register()
    {
        return $this->simpleView('auth.register');
    }

    public function userProfile($username)
    {
        $titrepage = 'Profile / Twitter';
        $css = "profile.css";

        $username = htmlspecialchars($username);
        $usrnm = $username;
        $username= '@'.$username;

        $profile_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $user = (new User($this->getDB()))->findByUsername($profile_username);
        $profile = (new User($this->getDB()))->findByUsername($username);
      
        if(!empty($profile->id)){
            $titrepage = $profile->name.' ('.$profile->username.') / Twitter';
            $countTweet = (new Tweet($this->getDB()))->countTweet($profile->id); 
            $followers = (new Follow($this->getDB()))->countFollow($profile->id);
            $following = (new Follow($this->getDB()))->countFollower($profile->id);
            
            $count = ['countFollow'=>$followers->count,'countFollower'=>$following->count,'countTweet'=>$countTweet->count];
            // var_dump($followers->count,$following->count,$countTweet->count);
    
            return $this->view('user.profile', compact('titrepage','css','count','profile','user','username','usrnm'));
        }
            return $this->view('user.profile',compact('titrepage','css','usrnm'));
    }

    public function registerPost()
    {
        $errors = [];
        $data = [];

        $name = htmlentities($_POST['Name_Lastname']);
     

        $NomPrenom = explode(' ', $name);
        $username = $NomPrenom[1] . $NomPrenom[0][0];
     

        // var_dump($replace);
        $email = htmlentities($_POST['Email']);
        

        $mois = htmlentities($_POST['mois']);
        $jour =  htmlentities($_POST['jour']);
        $annee =  htmlentities($_POST['annee']);
        $password = htmlentities($_POST['Password']);

        $birthdate = $annee . '-' . $mois . '-' . $jour;


        if (empty($name)) {
            $errors['name'] = "Veuillez entrer votre prénom";
        }
        if (empty($email)) {
            $errors['email'] = "Veuillez entrer votre email";
        }
        if (empty($password)) {
            $errors['password'] = "Veuillez entrer votre mot de passe";
        }
        if (empty($birthdate)) {
            $errors['date_of_birth'] = "Veuillez entrer votre date de naissance";
        }
        // if (strlen($password) < 8) {
        //     $errors['PasswordLenght'] = "Password not long enough! Must be at least 8 characters long";
        // }
        $selectUser = (new User($this->getDB()))->selectUser(1);
        $user = (new User($this->getDB()))->findByUsername($username);
        $userMail = (new User($this->getDB()))->findByEmail($email);
            var_dump($user, 'VARDUMP');
        if (!empty($user->id)) {
            $errors['usernameEXist'] = "Ce nom d'utilisateur existe deja";
        }
        if (!empty($userMail->id)) {
            $errors['emailEXist'] = "Cette adresse email est deja utilisee";
        }
        if (empty($errors)) {
            
            $hashed_password = hash('ripemd160', $password . 'vive le projet Tweet_academy');
            $username = htmlspecialchars($username);
            $username = '@'.$username;
            $user = (new User($this->getDB()))->InsertUser($username, $name, $email, $hashed_password, $birthdate);
            
            $data['success'] = true;
            $data['message'] = 'Success!';
            return header('Location: ' . BASE_NAME."login" );
            var_dump('marche');
        } 
        else 
        {
            $data['success'] = false;
            $data['errors'] = $errors;
            var_dump('rfefefe');
        }
        echo json_encode($data);
    }
    public function edit()
    {
        $titrepage = 'Edit Page - ';
        $css = "profile.css";
        
        if(isset($_SESSION['username'])){
            $username = htmlspecialchars($_SESSION['username']);
            $user = (new User($this->getDB()))->findByUsername($username);
            $titrepage = $user->name.' ('.$user->username.') / Twitter';
            return $this->view('user.edit', compact('titrepage','css','user'));
        }
        else{
            return header('Location: ' . BASE_NAME.'login');
        }
    }
    public function editPost()
    {
        $titrepage = 'Edit Page - ';
        
        return $this->view('user.edit', compact('titrepage'));
    }
}

<?php
namespace App\Controllers;
use App\Models\Follow;
use Database\DBConnection;

abstract class Controller
{
    protected $db;

    public function __construct(DBConnection $db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $db;
    }

    protected function simpleView(string $path, array $params = null)
    {
        $path = str_replace('.',DIRECTORY_SEPARATOR,$path);
        require VIEWS.$path.'.phtml';
    }

    protected function view(string $path, array $params = null)
    {
        if(isset($_SESSION['user_id'])){
            $params['ramdomFollow'] = (new Follow($this->getDB()))->randomFollow($_SESSION['user_id']);
            
        }
       
  
        ob_start();
        $path = str_replace('.',DIRECTORY_SEPARATOR,$path);
        require VIEWS.$path.'.phtml';
        
       $content = ob_get_clean();
       require VIEWS.'layout.phtml';
    }
    
    protected function getDB(){
        return $this->db;
    }

    protected function isAdmin()
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === 1) {
            return true;
        } else {
            return header('Location: '.BASE_NAME.'login');
        }
    }

    protected function randomFollower(){
        if(isset($_SESSION['user_id'])){
            $user_id = $_SESSION['user_id'];
            $randomFollow = (new Follow($this->getDB()))->randomFollow($user_id);
            
            var_dump($randomFollow);
            // die();
            return $randomFollow;
        }
    }
}
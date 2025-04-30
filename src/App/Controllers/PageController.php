<?php
namespace Paw\App\Controllers;

use Paw\Core\AbstractController;

class PageController extends AbstractController{

    public function index(){
        require $this->viewsDir . 'home.php';
    }

    public function aboutUs(){
        require $this->viewsDir . 'about-us.php';
    }

    public function login(){
        require $this->viewsDir . 'login.php';
    }


}


?>

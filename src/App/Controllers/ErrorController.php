<?php
namespace Paw\App\Controllers;

use Paw\Core\AbstractController;

class ErrorController extends AbstractController
{
    public function notFound(){
        require $this->viewsDir . 'errors/not-found.php';
        exit;
    }

    public function internalError(){
        require $this->viewsDir . 'errors/internalError.php';
        exit;
    }
}

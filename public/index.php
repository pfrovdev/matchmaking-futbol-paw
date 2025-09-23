<?php

require_once __DIR__ . "/../src/Core/Middelware/GlobalExceptionHandler.php";
require __DIR__ . "/../src/bootstrap.php";



$router->direct($request);

?>


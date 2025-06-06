<?php

namespace Paw\Core;

class Request
{
    public function uri()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        return $uri === '/' ? '/' : ltrim($uri, '/');
    }

    public function method()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function route(){
        return [
            "uri" => $this->uri(),
            "method" => $this->method()
        ];
    }
}

?>
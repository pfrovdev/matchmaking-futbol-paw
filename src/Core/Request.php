<?php

namespace Paw\Core;

class Request
{
    public function uri()
    {
        return ltrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');
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
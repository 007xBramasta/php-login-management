<?php

namespace Bramasta\Belajar\PHP\MVC\App 
{

    function header(string $value)
    {
        echo $value;
    }

}

namespace Bramasta\Belajar\PHP\MVC\Service
{
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}

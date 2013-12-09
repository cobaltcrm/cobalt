<?php

class JRoute
{
    public static function _($url)
    {
        return Cobalt\Router::to($url);
    }
}

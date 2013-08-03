<?php

class JRoute
{
    public static function _($url)
    {
        $router = Cobalt\Container::get('app')->getRouter();

        return $router->getRouteFor($url);
    }
}

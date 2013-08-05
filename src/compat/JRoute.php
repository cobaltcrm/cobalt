<?php

class JRoute
{
    public static function _($url)
    {
        /** @var \Cobalt\CobaltRouter $router */
        $router = Cobalt\Container::get('app')->getRouter();

        return $router->getRouteFor($url);
    }
}

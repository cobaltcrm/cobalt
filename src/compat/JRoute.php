<?php

class JRoute
{
    public static function _($url)
    {
        return Cobalt\CobaltRouter::to($url);
    }
}

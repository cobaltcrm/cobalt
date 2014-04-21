<?php
class CURI
{
    public static function base()
    {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'install/';
    }

}

<?php
class CURI
{
    public static function base()
    {
    	$currentUri = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        return substr($currentUri, 0, strrpos($currentUri, 'index.php'));
    }

}

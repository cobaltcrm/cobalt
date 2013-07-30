<?php
class CURI
{
    public static function base()
    {
        return substr(JURI::base(),0, strrpos(JURI::base(),"install/"));
    }

}

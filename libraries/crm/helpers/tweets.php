<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class CobaltHelperTweets extends JObject
{

    //Todo: Revisit buffer function to use Joomla Buffer
    function getTweets($twitter_user) 
    {

        $i = 0;
        $twitter_url = 'http://twitter.com/statuses/user_timeline/'.$twitter_user.'.xml?count=4';

        $tweets = array();

        if ( $buffer = @file_get_contents($twitter_url) ){

            libxml_use_internal_errors();
            try{
                $xml = @new SimpleXMLElement($buffer);

                if($xml->status) {
                    foreach ($xml->status as $status) {
                        $tweet =  $status -> text;
                        $date = $status -> created_at;
                        $id = $status -> id;

                        //Turn all urls, hastags, and @mentions into links              
                        $tweet = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\">\\2</a>", $tweet);
                        $tweet = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\">\\2</a>", $tweet);
                        $tweet = preg_replace("/@(\w+)/", "<a href=\"http://twitter.com/\\1\">@\\1</a>", $tweet);
                        $tweet = preg_replace("/#(\w+)/", "<span class='twitter_hash'><a href=\"http://search.twitter.com/search?q=\\1\">#\\1</a></span>", $tweet);

                        $formatted_date = CobalthelperDate::formatDate($date);                                         

                        $tweets[$i]['tweet'] = $tweet;
                        $tweets[$i]['date'] = $formatted_date;

                        $i++;
                    }
                }

            }catch(Exception $e){

            }

        }

        return $tweets;
    }

}
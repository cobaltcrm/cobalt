<?php

use Joomla\Input\Input as JInput;

class crmControllerInstall
{
    /** Validate database credentials **/
    public function validateDb()
    {
        //prevent display error in ajax response
        ini_set('display_errors', 0);

        //json
        $r = array();

        $input = new JInput;

        $model = new crmModelInstall;

        $db_name = $input->getCmd('name');
        $db_driver = $input->getCmd('db_drive');

        if (empty($db_name)) {
            $r['error'] = 'Please fill database name';
            $r['valid'] = false;
        } else {
            //Testing connection
            try {
                $db = $model->getDbo($db_driver,$input->getCmd('host'), $input->getUsername('user'), $input->getString('pass'), $db_name, '', false);
                $db->connect();
                if (!$db->connected()) {
                    $r['error'] = 'Cant connect with you database';
                    $r['valid'] = false;
                } else {
                    $r['valid'] = true;
                }


            } catch (Exception $e) {
                $r['error'] = $e->getMessage();
                $r['valid'] = false;
            }
        }

        //return
        echo json_encode($r);
    }

    /** Install application **/
    public function install()
    {
        //prevent display error for install
        ini_set('display_errors', 0);

        $model = new crmModelInstall();

        if ( !$model->install() )
        {
            JSession::getInstance('none', array())->set('error', $model->getError());
            header('Location: '.JUri::base());
        }

        // require_once JPATH_BASE . '/src/boot.php';

        // TODO login automatically
        // $app = JApplicationWeb::getInstance('cobalt',$model->getRegistry());
        // JFactory::$application = $app;
        // JFactory::$database = $model->getDb();
        // // Initialise the application.
        // $app->initialise();
        // $app->login($model->getAdmin());

        //REDIRECT TO ADMIN PAGE



        header('Location: '.JUri::root()."?view=cobalt");

    }

}


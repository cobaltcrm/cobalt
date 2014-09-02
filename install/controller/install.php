<?php

use Joomla\Input\Input as JInput;

class crmInstallController
{
    /** Validate database credentials **/
    public function validateDb()
    {
        //prevent display error in ajax repsonse
        ini_set('display_errors', 0);

        //json
        $r = array();

        //add check for mysqli
        if (!function_exists('mysqli')) {
            $r['error'] = 'Please enable mysqli!';
            $r['valid'] = false;
        }

        $input = new JInput;

        //connect
        $mysqli = new mysqli($input->getCmd('host'), $input->getUsername('user'), $input->getString('pass'));

        $db_name = $input->getCmd('name');

        if (empty($db_name)) {
            $r['error'] = 'Please fill database name';
            $r['valid'] = false;
        }

        //check mysql
        if ($mysqli->connect_errno) {
            $r['error'] = $mysqli->connect_error;
            $r['valid'] = false;
        } else {
            if ($mysqli->query("CREATE DATABASE IF NOT EXISTS ".$db_name)) {
                $r['error'] = $mysqli->connect_error;
                $r['valid'] = false;
            } else {
                if (!$mysqli->select_db($db_name)) {
                    $r['error'] = $mysqli->connect_error;
                    $r['valid'] = false;
                }
            }
            $r['valid'] = true;
        }

        //close
        $mysqli->close();

        //return
        echo json_encode($r);
    }

    /** Install application **/
    public function install()
    {

        //load our installation model
        include_once(JPATH_BASE."/install/model/install.php");
        include_once('helpers/uri.php');
        
        $model = new crmInstallModel();
        
        if ( !$model->install() )
        {
            session_start();
            $_SESSION['error'] = $model->getError();
            header('Location: '.CURI::base());
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
        header('Location: '.CURI::base()."?view=cobalt");

    }

}

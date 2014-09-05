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

        //add check for mysqli
        if (!function_exists('mysqli')) {
            $r['error'] = 'Please enable mysqli!';
            $r['valid'] = false;
        }

        $input = new JInput;

        $model = new crmModelInstall;

        $db_name = $input->getCmd('name');

        if (empty($db_name)) {
            $r['error'] = 'Please fill database name';
            $r['valid'] = false;
        }

        //Testing connection
        try {
            $db = $model->getDbo('mysqli',$input->getCmd('host'), $input->getUsername('user'), $input->getString('pass'), $db_name, '', false);
            $r['valid'] = true;
        } catch (Exception $e) {
            $r['error'] = $e->getMessage();
            $r['valid'] = false;
        }

        //return
        echo json_encode($r);
    }

    /** Install application **/
    public function install()
    {
        $model = new crmModelInstall();

        if ( !$model->install() )
        {
            JFactory::getApplication()->getSession()->set('error', $model->getError());
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


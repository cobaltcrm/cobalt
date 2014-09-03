<?php

use Joomla\Input\Input as JInput;

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'install.php';

class crmInstallController
{
    /** Validate database credentials **/
    public function validateDb()
    {
        //prevent display error in ajax repsonse
        ini_set('display_errors', 0);

        //json
        $r = array();

        $input = new JInput;

        //connect
        $model = new crmInstallModel;

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

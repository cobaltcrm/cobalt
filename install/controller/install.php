<?php

class crmInstallController
{
    /** Validate database credentials **/
	public function validateDb()
    {
        //json
        $r = array();

		include_once(JPATH_BASE."/install/model/install.php");

		$model = new crmInstallModel();

		try {
			// Get a database object.
			$db = $model->getDBO('mysqli',$_POST['host'],$_POST['user'],$_POST['pass'],$_POST['name'],$_POST['prefix'],true);
		}
		catch (Exception $e)
		{
			$r['error'] = $e->getMessage();
			$r['valid'] = false;
		}

		$db->connect();

		if ( $db->connected() )
		{
			$r['valid'] = true;
		}

		$db->disconnect();

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

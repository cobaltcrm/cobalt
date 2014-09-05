<?php
class crmApplication extends JApplicationWeb
{
    /**
     * Execute Application
     */
    public function doExecute()
    {
        $this->loadSession();
        JFactory::$application = $this;

        $this->error = $this->session->get('error');

        $controller = $this->input->getCmd('c');
        $method = $this->input->getCmd('m');

        if (!empty($controller) && !empty($method)) {
            $controller_name = sprintf('crmController%s',ucfirst($controller));
            $instance = new $controller_name();
            if (!method_exists($instance, $method)) {
                die(sprintf('Invalid action "%s" for %s.',$method,$controller_name));
            }
            $instance->$method();
        } else {
            include_once JPATH_INSTALLATION.'/view/default.php';
        }
    }
}
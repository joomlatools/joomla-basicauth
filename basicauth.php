<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemBasicAuth extends JPlugin
{
    public function __construct($subject, $config = array())
    {
        $input = JFactory::getApplication()->input;

        if(strpos(PHP_SAPI, 'cgi') !== false) {
            $authorization = $input->server->get('REDIRECT_HTTP_AUTHORIZATION', null, 'string');
        } else {
            $authorization = $input->server->get('HTTP_AUTHORIZATION', null, 'string');
        }

        if(strstr($authorization, 'Basic'))
        {
            $parts = explode(':', base64_decode(substr($authorization, 6)));

            if(count($parts) == 2)
            {
                $input->server->set('PHP_AUTH_USER', $parts[0]);
                $input->server->set('PHP_AUTH_PW', $parts[0]);
            }
        }

        parent::__construct($subject, $config);
    }

    public function onAfterRoute()
    {
        $user = JFactory::getUser();

        if(!$user->get('guest')) {
            return;
        }

        $app    = JFactory::getApplication();
        $menu   = $app->getMenu();
        $active = $menu->getActive();

        if(!is_object($active) || !$active->id) {
            return;
        }

        if($active->access == 2 || $active->access == 3)
        {
            $username = $app->input->server->get('PHP_AUTH_USER', null, 'string');
            $password = $app->input->server->get('PHP_AUTH_PW', null, 'string');

            if(empty($username) || empty($password)) {
                $this->_requestAuthentication();
            }

            $credentials = array(
                'username' => $username,
                'password' => $password
            );

            if(JFactory::getApplication()->login($credentials) !== true)
            {
                throw new KException('Login failed', KHttpResponse::UNAUTHORIZED);
                return false;
            }
        }
    }

    protected function _requestAuthentication()
    {
        $realm = JFactory::getConfig()->get('sitename');

        header('WWW-Authenticate: Basic realm="'.$realm.'"');
        header('HTTP/1.0 401 Unauthorized');

        echo "Unauthorized";
        exit;
    }
}
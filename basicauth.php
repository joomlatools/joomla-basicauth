<?php
/**
 * Basic HTTP authentication for Joomla - https://github.com/joomlatools/joomla-plg_basicauth
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomla-plg_basicauth for the canonical source repository
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Basic HTTP auth plugin
 *
 * @author      Steven Rombauts <https://github.com/stevenrombauts>
 * @package     Joomla.Plugin
 * @subpackage  System.basicauth
 */
class plgSystemBasicAuth extends JPlugin
{
    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An array that holds the plugin configuration
     */
    public function __construct($subject, $config = array())
    {
        $input = JFactory::getApplication()->input;

        // See if the client has sent authorization headers
        if(strpos(PHP_SAPI, 'cgi') !== false) {
            $authorization = $input->server->get('REDIRECT_HTTP_AUTHORIZATION', null, 'string');
        } else {
            $authorization = $input->server->get('HTTP_AUTHORIZATION', null, 'string');
        }

        // If basic authorization is available, store the username and password in the $_SERVER globals
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

    /**
     * Ask for authentication and log the user in into the application.
     *
     * @return  void
     */
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

        // Check for authorization if the active menu item has the right access level
        if($active->access == $this->params->get('access'))
        {
            $username = $app->input->server->get('PHP_AUTH_USER', null, 'string');
            $password = $app->input->server->get('PHP_AUTH_PW', null, 'string');

            // If no credentials are passed, respond with the authentication headers
            if(empty($username) || empty($password)) {
                $this->_requestAuthentication();
            }

            $credentials = array(
                'username' => $username,
                'password' => $password
            );

            // If we did receive the user credentials from the user, try to login
            if(JFactory::getApplication()->login($credentials) !== true)
            {
                throw new KException('Login failed', KHttpResponse::UNAUTHORIZED);
                return false;
            }
        }
    }

    /**
     * Push the authenticate headers back to user and ask for authorization.
     */
    protected function _requestAuthentication()
    {
        $realm = JFactory::getConfig()->get('sitename');

        header('WWW-Authenticate: Basic realm="'.$realm.'"');
        header('HTTP/1.0 401 Unauthorized');

        echo "Unauthorized";
        exit;
    }
}
<?php
/**
 * Basic HTTP authentication for Joomla - https://github.com/joomlatools/joomla-basicauth
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomla-basicauth for the canonical source repository
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
                $input->server->set('PHP_AUTH_PW', $parts[1]);
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
        $app = JFactory::getApplication();

        $username = $app->input->server->get('PHP_AUTH_USER', null, 'string');
        $password = $app->input->server->get('PHP_AUTH_PW', null, 'string');

        if ($username && $password)
        {
            if (!$this->_login($username, $password, $app)) {
                throw new Exception('Login failed', 401);
            }
        }
    }

    /**
     * Logs in a given user to an application.
     *
     * @param string $username    The username.
     * @param string $password    The password.
     * @param object $application The application.
     *
     * @return bool True if login was successful, false otherwise.
     */
    protected function _login($username, $password, $application)
    {
        $result = false;

        // If we did receive the user credentials from the user, try to login
        if($application->login(array('username' => $username, 'password' => $password)) === true)
        {
            if (class_exists('Koowa')) {
                KObjectManager::getInstance()->getObject('user')->setAuthentic(); // Explicitly authenticate user
            }

            $result = true;
        }

        return $result;
    }
}
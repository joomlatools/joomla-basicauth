<?php
/**
 * Basic HTTP authentication for Joomla - https://github.com/joomlatools/joomla-basicauth
 *
 * @copyright	Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomla-basicauth for the canonical source repository
 */

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
     * Log user in from the basic authentication credentials in the request if possible
     *
     * onAfterInitialise is used here to make sure that Joomla doesn't display error messages for menu items
     * with registered and above access levels.
     */
    public function onAfterInitialise()
    {
        if (class_exists('Koowa') && JFactory::getUser()->guest)
        {
            $manager = KObjectManager::getInstance();
            $basic   = $manager->getObject('com:koowa.dispatcher.authenticator.basic');

            if ($basic->getUsername()) {
                $basic->authenticateRequest($manager->getObject('com:koowa.dispatcher')->getContext());
            }
        }
    }
}
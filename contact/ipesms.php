<?php

/**
 * Modules contact sms plugin
 * 
 * PHP version 5.6.x | 7.x | 8.x
 * 
 * @category  Plugins
 * @package   Joomla
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla helper
jimport('joomla.application.component.helper');

// Load SMS Notification Model
jimport('joomla.application.component.model');

/**
 * Contact plugin for sms_notification Class
 * 
 * @category  Plugins
 * @package   Joomla
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class PlgContactIPESMS extends JPlugin
{
    protected $autoloadLanguage = true;
    
    /**
     * Method to get the record form
     *
     * @param string $contact contact
     * @param array  $data    contact array data
     * 
     * @return boolean
     */      
    function onSubmitContact($contact, $data)
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_smsnotification/models');
        
        $ipesmsModel = JModelLegacy::getInstance('SMSNotification', 'SMSNotificationModel');
        
        $params = JComponentHelper::getParams('com_smsnotification');
        $enabled = $params->get('email_alert_enabled');
        
        if ($enabled == 'false') {
            return;
        }
      
        $phoneNumber[] = $params->get('admin_phone_number');
        $alertParams = $params->get('email_alert_parameters');
        
        $parameterStrings = array();
        
        $name = $data['contact_name'];
        $email = $data['contact_email'];
        $subject = $data['contact_subject'];
        $message = $data['contact_message'];
        
        array_push($parameterStrings, JText::_('PLG_CONTACT_SMS_NOTIFICATION_CONTACT_DESC'));

        foreach ($alertParams as $alertParam) {
            switch ($alertParam) {
            case 'name':
                array_push($parameterStrings, JText::_('PLG_CONTACT_SMS_NOTIFICATION_NAME') .' : ' . $name);
                break;
            case 'email':
                array_push($parameterStrings, JText::_('PLG_CONTACT_SMS_NOTIFICATION_EMAIL') .' : ' . $email);
                break;
            case 'subject':
                array_push($parameterStrings, JText::_('PLG_CONTACT_SMS_NOTIFICATION_SUBJECT') .' : ' . $subject);
                break;
            case 'message':
                array_push($parameterStrings, JText::_('PLG_CONTACT_SMS_NOTIFICATION_MESSAGE') .' : ' . $message);
                break;
            }
        }
        
        $alertMessage = implode("\r\n", $parameterStrings);
                        
        if ($phoneNumber != "" && $alertMessage != "") {
            $ipesmsModel->sendIPESMS($phoneNumber, $alertMessage, '1');
        }
    }
}
?>
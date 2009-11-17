<?php
/**
 * $Id: pool_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../subscription.class.php';
require_once dirname(__FILE__) . '/../reservation.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class PoolForm extends FormValidator
{
    private $user;

    function PoolForm($action, $user)
    {
        parent :: __construct('pool_form', 'post', $action);
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Pool') . '</span>');
        
        $this->add_timewindow(Subscription :: PROPERTY_START_TIME, Subscription :: PROPERTY_STOP_TIME, Translation :: get('StartDate'), Translation :: get('StopDate'));
        
        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
        //		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('html', '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/pool.js' . '"></script>');
        
        $this->addElement('html', '<div class="clear"></div>');
        $this->addElement('html', '</div>');
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $now = time();
        $defaults[Subscription :: PROPERTY_START_TIME] = $now;
        $defaults[Subscription :: PROPERTY_STOP_TIME] = strtotime('+1 Hour', $now);
        parent :: setDefaults($defaults);
    }
}
?>
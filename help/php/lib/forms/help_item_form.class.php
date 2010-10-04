<?php
/**
 * $Id: help_item_form.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.forms
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

class HelpItemForm extends FormValidator
{
    private $help_item;

    function HelpItemForm($help_item, $action)
    {
        parent :: __construct('help_item', 'post', $action);
        
        $this->help_item = $help_item;
        $this->build_basic_form();
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('text', HelpItem :: PROPERTY_URL, Translation :: get('Url'), array('size' => '100'));
        $this->addRule(HelpItem :: PROPERTY_URL, Translation :: get('ThisFieldIsRequired'), 'required');
        //$this->addElement('submit', 'help_item_settings', 'OK');
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('hidden', HelpItem :: PROPERTY_NAME);
    }

    function update_help_item()
    {
        $help_item = $this->help_item;
        $values = $this->exportValues();
        
        $help_item->set_name($values[HelpItem :: PROPERTY_NAME]);
        $help_item->set_url($values[HelpItem :: PROPERTY_URL]);
        
        return $help_item->update();
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $help_item = $this->help_item;
        $defaults[HelpItem :: PROPERTY_NAME] = $help_item->get_name();
        $defaults[HelpItem :: PROPERTY_URL] = $help_item->get_url();
        parent :: setDefaults($defaults);
    }

    function get_help_item()
    {
        return $this->help_item;
    }
}
?>
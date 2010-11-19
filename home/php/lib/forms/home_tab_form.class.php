<?php
namespace home;
use common\libraries\Translation;
use common\libraries\FormValidator;
/**
 * $Id: home_tab_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class HomeTabForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $hometab;
    private $form_type;

    function __construct($form_type, $hometab, $action)
    {
        parent :: __construct('home_tab', 'post', $action);
        
        $this->hometab = $hometab;
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('text', HomeTab :: PROPERTY_TITLE, Translation :: get('HomeTabTitle'), array("size" => "50"));
        $this->addRule(HomeTab :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');
        
        $this->addElement('hidden', HomeTab :: PROPERTY_USER);
        
    //$this->addElement('submit', 'home_tab', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', HomeTab :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities::COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities::COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_object()
    {
        $hometab = $this->hometab;
        $values = $this->exportValues();
        
        $hometab->set_title($values[HomeTab :: PROPERTY_TITLE]);
        
        return $hometab->update();
    }

    function create_object()
    {
        $hometab = $this->hometab;
        $values = $this->exportValues();
        
        $hometab->set_title($values[HomeTab :: PROPERTY_TITLE]);
        
        return $hometab->create();
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $hometab = $this->hometab;
        $defaults[HomeTab :: PROPERTY_ID] = $hometab->get_id();
        $defaults[HomeTab :: PROPERTY_TITLE] = $hometab->get_title();
        $defaults[HomeTab :: PROPERTY_USER] = $hometab->get_user();
        parent :: setDefaults($defaults);
    }
}
?>
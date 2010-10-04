<?php
/**
 * $Id: home_row_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class HomeRowForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $homerow;
    private $form_type;

    function HomeRowForm($form_type, $homerow, $action)
    {
        parent :: __construct('home_row', 'post', $action);
        
        $this->homerow = $homerow;
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
        $this->addElement('text', HomeRow :: PROPERTY_TITLE, Translation :: get('HomeRowTitle'), array("size" => "50"));
        $this->addRule(HomeRow :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', HomeRow :: PROPERTY_TAB, Translation :: get('HomeRowTab'), $this->get_tabs());
        $this->addRule(HomeRow :: PROPERTY_TAB, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('hidden', HomeRow :: PROPERTY_USER);
        
    //$this->addElement('submit', 'home_row', Translation :: get('Ok'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', HomeRow :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_object()
    {
        $homerow = $this->homerow;
        $values = $this->exportValues();
        
        $homerow->set_title($values[HomeRow :: PROPERTY_TITLE]);
        $homerow->set_tab($values[HomeRow :: PROPERTY_TAB]);
        
        return $homerow->update();
    }

    function create_object()
    {
        $homerow = $this->homerow;
        $values = $this->exportValues();
        
        $homerow->set_title($values[HomeRow :: PROPERTY_TITLE]);
        $homerow->set_tab($values[HomeRow :: PROPERTY_TAB]);
        
        return $homerow->create();
    }

    function get_tabs()
    {
        $user_id = $this->homerow->get_user();
        $condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
        
        $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($condition);
        $tab_options = array();
        while ($tab = $tabs->next_result())
        {
            $tab_options[$tab->get_id()] = $tab->get_title();
        }
        
        return $tab_options;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $homerow = $this->homerow;
        $defaults[HomeRow :: PROPERTY_ID] = $homerow->get_id();
        $defaults[HomeRow :: PROPERTY_TITLE] = $homerow->get_title();
        $defaults[HomeRow :: PROPERTY_TAB] = $homerow->get_tab();
        $defaults[HomeRow :: PROPERTY_USER] = $homerow->get_user();
        parent :: setDefaults($defaults);
    }
}
?>
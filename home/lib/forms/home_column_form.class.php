<?php
/**
 * $Id: home_column_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class HomeColumnForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $homecolumn;
    private $form_type;

    function HomeColumnForm($form_type, $homecolumn, $action)
    {
        parent :: __construct('home_column', 'post', $action);
        
        $this->homecolumn = $homecolumn;
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
        $this->addElement('text', HomeColumn :: PROPERTY_TITLE, Translation :: get('HomeColumnTitle'), array("size" => "50"));
        $this->addRule(HomeColumn :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', HomeColumn :: PROPERTY_ROW, Translation :: get('HomeColumnRow'), $this->get_rows());
        $this->addRule(HomeColumn :: PROPERTY_ROW, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('text', HomeColumn :: PROPERTY_WIDTH, Translation :: get('HomeColumnWidth'), array("size" => "50"));
        $this->addRule(HomeColumn :: PROPERTY_WIDTH, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('hidden', HomeColumn :: PROPERTY_USER);
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', HomeColumn :: PROPERTY_ID);
        $this->addRule(HomeColumn :: PROPERTY_WIDTH, Translation :: get('MaxValue'), 'max_value', $this->exportValues());
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        $this->addRule(HomeColumn :: PROPERTY_WIDTH, Translation :: get('MaxValue'), 'max_value', $this->exportValues());
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_object()
    {
        $homecolumn = $this->homecolumn;
        $values = $this->exportValues();
        
        $homecolumn->set_title($values[HomeColumn :: PROPERTY_TITLE]);
        $homecolumn->set_row($values[HomeColumn :: PROPERTY_ROW]);
        $homecolumn->set_width($values[HomeColumn :: PROPERTY_WIDTH]);
        
        return $homecolumn->update();
    }

    function create_object()
    {
        $homecolumn = $this->homecolumn;
        $values = $this->exportValues();
        
        $homecolumn->set_title($values[HomeColumn :: PROPERTY_TITLE]);
        $homecolumn->set_row($values[HomeColumn :: PROPERTY_ROW]);
        $homecolumn->set_width($values[HomeColumn :: PROPERTY_WIDTH]);
        
        return $homecolumn->create();
    }

    function get_rows()
    {
        $user_id = $this->homecolumn->get_user();
        $condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
        
        $rows = HomeDataManager :: get_instance()->retrieve_home_rows($condition);
        $row_options = array();
        while ($row = $rows->next_result())
        {
            $row_options[$row->get_id()] = $row->get_title();
        }
        
        return $row_options;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $homecolumn = $this->homecolumn;
        $defaults[HomeColumn :: PROPERTY_ID] = $homecolumn->get_id();
        $defaults[HomeColumn :: PROPERTY_TITLE] = $homecolumn->get_title();
        $defaults[HomeColumn :: PROPERTY_ROW] = $homecolumn->get_row();
        $defaults[HomeColumn :: PROPERTY_WIDTH] = $homecolumn->get_width();
        $defaults[HomeColumn :: PROPERTY_USER] = $homecolumn->get_user();
        
        parent :: setDefaults($defaults);
    }
}
?>
<?php
/**
 * $Id: home_block_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class HomeBlockForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $homeblock;
    private $form_type;

    function HomeBlockForm($form_type, $homeblock, $action)
    {
        parent :: __construct('home_block', 'post', $action);
        
        $this->homeblock = $homeblock;
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
        $this->addElement('text', HomeBlock :: PROPERTY_TITLE, Translation :: get('HomeBlockTitle'), array("size" => "50"));
        $this->addRule(HomeBlock :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        /*$this->addElement('select', HomeBlock :: PROPERTY_, Translation :: get('HomeBlockColumn'), $this->get_columns());
        $this->addRule(HomeBlock :: PROPERTY_COLUMN, Translation :: get('ThisFieldIsRequired'), 'required');*/
        
        $this->addElement('select', HomeBlock :: PROPERTY_COLUMN, Translation :: get('HomeBlockColumn'), $this->get_columns());
        $this->addRule(HomeBlock :: PROPERTY_COLUMN, Translation :: get('ThisFieldIsRequired'), 'required');
        
        //$blocks = Block :: get_platform_blocks();
        

        //$select = $this->addElement('hierselect', HomeBlock :: PROPERTY_COMPONENT, Translation :: get('HomeBlockComponent'));
        //$select->setOptions(array($blocks['applications'], $blocks['components']));
        

        $this->addElement('select', HomeBlock :: PROPERTY_COMPONENT, Translation :: get('HomeBlockComponent'), Block :: get_platform_blocks_deprecated());
        $this->addRule(HomeBlock :: PROPERTY_COMPONENT, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('hidden', HomeBlock :: PROPERTY_USER);
        
    //$this->addElement('submit', 'home_block', Translation :: get('Ok'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $block = $this->getElement(HomeBlock :: PROPERTY_COMPONENT);
        //$block->freeze();
        $this->addElement('hidden', HomeBlock :: PROPERTY_ID);
        
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
        $homeblock = $this->homeblock;
        $values = $this->exportValues();
        $component = explode('.', $values[HomeBlock :: PROPERTY_COMPONENT]);
        $hdm = HomeDataManager :: get_instance();
        
        if ($homeblock->get_application() != $component[0] || $homeblock->get_component() != $component[1])
        {
            if (! $hdm->delete_home_block_configs($homeblock))
            {
                return false;
            }
            
            if (! $hdm->create_block_properties($homeblock))
            {
                return false;
            }
        }
        
        $homeblock->set_title($values[HomeBlock :: PROPERTY_TITLE]);
        $homeblock->set_column($values[HomeBlock :: PROPERTY_COLUMN]);
        $homeblock->set_application($component[0]);
        $homeblock->set_component($component[1]);
        
        return $homeblock->update();
    }

    function create_object()
    {
        $homeblock = $this->homeblock;
        $values = $this->exportValues();
        
        $component = explode('.', $values[HomeBlock :: PROPERTY_COMPONENT]);
        
        $homeblock->set_title($values[HomeBlock :: PROPERTY_TITLE]);
        $homeblock->set_column($values[HomeBlock :: PROPERTY_COLUMN]);
        $homeblock->set_application($component[0]);
        $homeblock->set_component($component[1]);
        
        if (! $homeblock->create())
        {
            return false;
        }
        
        $success_config = HomeDataManager :: get_instance()->create_block_properties($homeblock);
        
        if (! $success_config)
        {
            return false;
        }
        
        return true;
    }

    function get_columns()
    {
        $user_id = $this->homeblock->get_user();
        $condition = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        
        $columns = HomeDataManager :: get_instance()->retrieve_home_columns($condition);
        $column_options = array();
        while ($column = $columns->next_result())
        {
            $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $column->get_row());
            $condition = new SubselectCondition(HomeTab :: PROPERTY_ID, HomeRow :: PROPERTY_TAB, HomeRow :: get_table_name(), $condition);
            
            $tab = HomeDataManager :: get_instance()->retrieve_home_tabs($condition)->next_result();
            
            if ($tab)
                $name = Translation :: get('Tab') . ' ' . $tab->get_title() . ' :';
            
            $column_options[$column->get_id()] = $name . $column->get_title();
        }
        
        return $column_options;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $homeblock = $this->homeblock;
        $defaults[HomeBlock :: PROPERTY_ID] = $homeblock->get_id();
        $defaults[HomeBlock :: PROPERTY_TITLE] = $homeblock->get_title();
        $defaults[HomeBlock :: PROPERTY_COLUMN] = $homeblock->get_column();
        $defaults[HomeBlock :: PROPERTY_COMPONENT] = $homeblock->get_component();
        $defaults[HomeBlock :: PROPERTY_USER] = $homeblock->get_user();
        parent :: setDefaults($defaults);
    }
}
?>
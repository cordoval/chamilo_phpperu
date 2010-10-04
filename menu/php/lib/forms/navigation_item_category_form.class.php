<?php
/**
 * $Id: navigation_item_category_form.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib
 */

class NavigationItemCategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $menuitem;

    function NavigationItemCategoryForm($form_type, $menuitem, $action)
    {
        parent :: __construct('navigation_item', 'post', $action);
        
        $this->menuitem = $menuitem;
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
        $this->addElement('text', NavigationItem :: PROPERTY_TITLE, Translation :: get('NavigationItemTitle'), array("size" => "50"));
        $this->addRule(NavigationItem :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', NavigationItem :: PROPERTY_ID);
        
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

    function update_navigation_item()
    {
        $menuitem = $this->menuitem;
        $values = $this->exportValues();
        
        $menuitem->set_title($values[NavigationItem :: PROPERTY_TITLE]);
        
        return $menuitem->update();
    }

    function create_navigation_item()
    {
        $menuitem = $this->menuitem;
        $values = $this->exportValues();
        
        $menuitem->set_title($values[NavigationItem :: PROPERTY_TITLE]);
        $menuitem->set_category(0);
        $menuitem->set_is_category(1);
        $menuitem->set_application('root');
        $menuitem->set_section('root');
        
        return $menuitem->create();
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $menuitem = $this->menuitem;
        $defaults[NavigationItem :: PROPERTY_TITLE] = $menuitem->get_title();
        parent :: setDefaults($defaults);
    }

    function get_navigation_item()
    {
        return $this->menuitem;
    }
}
?>
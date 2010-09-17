<?php

class InternshipOrganizerCategoryMoveForm extends FormValidator
{
    const PROPERTY_LOCATION = 'location';
    
    private $category;
    private $locations = array();
    private $level = 1;
    private $gdm;

    function InternshipOrganizerCategoryMoveForm($category, $action, $user)
    {
        parent :: __construct('category_move', 'post', $action);
        $this->category = $category;
        
        $this->build_form();
        
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('select', self :: PROPERTY_LOCATION, Translation :: get('NewLocation'), $this->get_categories());
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function move_category()
    {
        $new_parent = $this->exportValue(self :: PROPERTY_LOCATION);
        
        if ($new_parent != $this->category->get_id())
        {
            $this->category->set_parent_id($new_parent);
            return $this->category->update();
        }
        else
        {
            return false;
        }
    
    }

    function get_new_parent()
    {
        return $this->exportValue(self :: PROPERTY_LOCATION);
    }

    function get_categories()
    {
        $category = $this->category;
        
        $category_menu = new InternshipOrganizerCategoryMenu($category->get_id(), null, true, true);
        $renderer = new OptionsMenuRenderer();
        $category_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $category = $this->category;
        $defaults[self :: PROPERTY_LOCATION] = $category->get_parent_id();
        parent :: setDefaults($defaults);
    }
}
?>
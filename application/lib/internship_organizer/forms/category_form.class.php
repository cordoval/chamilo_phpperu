<?php

class InternshipOrganizerCategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'InternshipOrganizerCategoryUpdated';
    const RESULT_ERROR = 'InternshipOrganizerCategoryUpdateFailed';
    
    private $parent;
    private $category;
    private $user;

    function InternshipOrganizerCategoryForm($form_type, $category, $action, $user)
    {
        parent :: __construct('create_category', 'post', $action);
        
        $this->category = $category;
        $this->user = $user;
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
        $this->addElement('text', InternshipOrganizerCategory :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerCategory :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', InternshipOrganizerCategory :: PROPERTY_PARENT_ID, Translation :: get('Category'), $this->get_categories());
        $this->addRule(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        //$this->add_html_editor(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        

        $this->addElement('textarea', InternshipOrganizerCategory :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "5", "cols" => "40"));
        $this->addRule(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION);
    
    }

    function build_editing_form()
    {
        $category = $this->category;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', InternshipOrganizerCategory :: PROPERTY_ID);
        
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

    function update_category()
    {
        $category = $this->category;
        $values = $this->exportValues();
        
        $category->set_name($values[InternshipOrganizerCategory :: PROPERTY_NAME]);
        $category->set_description($values[InternshipOrganizerCategory :: PROPERTY_DESCRIPTION]);
        $value = $category->update();
        
        $new_parent = $values[InternshipOrganizerCategory :: PROPERTY_PARENT_ID];
        if ($category->get_parent_id() != $new_parent)
        {
            $category->move($new_parent);
        }
        
        //        if ($value)
        //        {
        //            Events :: trigger_event('update', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    function create_category()
    {
        $category = $this->category;
        $values = $this->exportValues();
        
        $category->set_name($values[InternshipOrganizerCategory :: PROPERTY_NAME]);
        $category->set_description($values[InternshipOrganizerCategory :: PROPERTY_DESCRIPTION]);
        $category->set_parent_id($values[InternshipOrganizerCategory :: PROPERTY_PARENT_ID]);
        
        $value = $category->create();
        
        //        if ($value)
        //        {
        //            Events :: trigger_event('create', 'category', array('target_category_id' => $category->get_id(), 'action_user_id' => $this->user->get_id()));
        //        }
        

        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $category = $this->category;
        $defaults[InternshipOrganizerCategory :: PROPERTY_ID] = $category->get_id();
        $defaults[InternshipOrganizerCategory :: PROPERTY_PARENT_ID] = $category->get_parent_id();
        $defaults[InternshipOrganizerCategory :: PROPERTY_NAME] = $category->get_name();
        $defaults[InternshipOrganizerCategory :: PROPERTY_DESCRIPTION] = $category->get_description();
        parent :: setDefaults($defaults);
    }

    function get_category()
    {
        return $this->category;
    }

    function get_categories()
    {
        $category = $this->category;
        
        $category_menu = new InternshipOrganizerCategoryMenu($category->get_id(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $category_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }
}
?>
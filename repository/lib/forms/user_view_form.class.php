<?php
/**
 * $Id: user_view_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */
class UserViewForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $user_view;
    private $form_type;

    function UserViewForm($form_type, $user_view, $action)
    {
        parent :: __construct('user_views_settings', 'post', $action);
        
        $this->user_view = $user_view;
        
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
        $this->addElement('text', UserView :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(UserView :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(UserView :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $uvrlo = RepositoryDataManager :: get_instance()->retrieve_user_view_rel_content_objects(new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $this->get_user_view()->get_id()));
            while ($type = $uvrlo->next_result())
            {
                $content_object_types[$type->get_content_object_type()] = Translation :: get(Utilities :: underscores_to_camelcase($type->get_content_object_type()) . 'TypeName');
                if ($type->get_visibility())
                    $defaults[] = $type->get_content_object_type();
            }
        }
        else
        {
            $dm = RepositoryDataManager :: get_instance();
            $registrations = $dm->get_registered_types();
            
            $hidden_types = array('learning_path_item', 'portfolio_item');
            
            foreach ($registrations as $registration)
            {
                if (in_array($registration, $hidden_types))
                    continue;
                $content_object_types[$registration] = Translation :: get(Utilities :: underscores_to_camelcase($registration) . 'TypeName');
                //$defaults[] = $registration;
            }
        }
        
        $this->addElement('advmultiselect', 'types', Translation :: get('SelectTypesToShow'), $content_object_types, array('style' => 'width:300px; height: 300px'));
        
        $this->setDefaults(array('types' => $defaults));
        
    //$this->addElement('submit', 'user_view_settings', 'OK');
    }

    function build_editing_form()
    {
        $user_view = $this->user_view;
        $this->build_basic_form();
        
        $this->addElement('hidden', UserView :: PROPERTY_ID);
        
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

    function update_user_view()
    {
        $user_view = $this->user_view;
        $values = $this->exportValues();
        
        $user_view->set_name($values[UserView :: PROPERTY_NAME]);
        $user_view->set_description($values[UserView :: PROPERTY_DESCRIPTION]);
        
        $dm = RepositoryDataManager :: get_instance();
        
        $dm->reset_user_view($user_view);
        
        foreach ($values['types'] as $type)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $user_view->get_id());
            $conditions[] = new EqualityCondition(UserViewRelContentObject :: PROPERTY_CONTENT_OBJECT_TYPE, $type);
            $condition = new AndCondition($conditions);
            
            $lo_type = $dm->retrieve_user_view_rel_content_objects($condition)->next_result();
            $lo_type->set_visibility(1);
            $lo_type->update();
        }
        
        $value = $user_view->update();
        
        return $value;
    }

    function create_user_view()
    {
        $user_view = $this->user_view;
        $values = $this->exportValues();
        
        $user_view->set_name($values[UserView :: PROPERTY_NAME]);
        $user_view->set_description($values[UserView :: PROPERTY_DESCRIPTION]);
        
        $value = $user_view->create($values['types']);
        
        return $value;
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $user_view = $this->user_view;
        $defaults[UserView :: PROPERTY_ID] = $user_view->get_id();
        $defaults[UserView :: PROPERTY_NAME] = $user_view->get_name();
        $defaults[UserView :: PROPERTY_DESCRIPTION] = $user_view->get_description();
        parent :: setDefaults($defaults);
    }

    function get_user_view()
    {
        return $this->user_view;
    }
}
?>
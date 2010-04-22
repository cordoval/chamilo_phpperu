<?php

class InternshipOrganizerRegionForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'InternshipOrganizerRegionUpdated';
    const RESULT_ERROR = 'InternshipOrganizerRegionUpdateFailed';
    
    private $parent;
    private $region;
   	private $user;

    function InternshipOrganizerRegionForm($form_type, $region, $action, $user)
    {
        parent :: __construct('create_region', 'post', $action);
        
        $this->region = $region;
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
        $this->addElement('text', InternshipOrganizerRegion :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerRegion :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
               
        $this->addElement('select', InternshipOrganizerRegion :: PROPERTY_PARENT_ID, Translation :: get('Region'), $this->get_regions());
        $this->addRule(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
    
    }

    function build_editing_form()
    {
        $region = $this->region;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', InternshipOrganizerRegion :: PROPERTY_ID);
        
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

    function update_region()
    {
        $region = $this->region;
        $values = $this->exportValues();
        
        $region->set_name($values[InternshipOrganizerRegion :: PROPERTY_NAME]);
        $region->set_description($values[InternshipOrganizerRegion :: PROPERTY_DESCRIPTION]);
        $value = $region->update();
        
        $new_parent = $values[InternshipOrganizerRegion :: PROPERTY_PARENT_ID];
        if ($region->get_parent_id() != $new_parent)
        {
            $region->move($new_parent);
        }
               
//        if ($value)
//        {
//            Events :: trigger_event('update', 'region', array('target_region_id' => $region->get_id(), 'action_user_id' => $this->user->get_id()));
//        }
        
        return $value;
    }

    function create_region()
    {
        $region = $this->region;
        $values = $this->exportValues();
        
        $region->set_name($values[InternshipOrganizerRegion :: PROPERTY_NAME]);
        $region->set_description($values[InternshipOrganizerRegion :: PROPERTY_DESCRIPTION]);
        $region->set_parent_id($values[InternshipOrganizerRegion :: PROPERTY_PARENT_ID]);
        
        $value = $region->create();
               
//        if ($value)
//        {
//            Events :: trigger_event('create', 'region', array('target_region_id' => $region->get_id(), 'action_user_id' => $this->user->get_id()));
//        }
        
        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $region = $this->region;
        $defaults[InternshipOrganizerRegion :: PROPERTY_ID] = $region->get_id();
        $defaults[InternshipOrganizerRegion :: PROPERTY_PARENT_ID] = $region->get_parent_id();
        $defaults[InternshipOrganizerRegion :: PROPERTY_NAME] = $region->get_name();
        $defaults[InternshipOrganizerRegion :: PROPERTY_DESCRIPTION] = $region->get_description();
        parent :: setDefaults($defaults);
    }

    function get_region()
    {
        return $this->region;
    }

    function get_regions()
    {
        $region = $this->region;
        
        $region_menu = new InternshipOrganizerRegionMenu($region->get_id(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $region_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }
}
?>
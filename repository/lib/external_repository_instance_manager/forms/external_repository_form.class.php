<?php
/**
 * $Id: external_repository_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.forms
 */

class ExternalRepositoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $external_repository;
    private $form_type;

    function ExternalRepositoryForm($form_type, $external_repository, $action)
    {
        parent :: __construct('external_repository', 'post', $action);
        
        $this->external_repository = $external_repository;
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
        $this->addElement('select', ExternalRepository :: PROPERTY_TYPE, Translation :: get('ExternalRepositoryType'), $this->get_external_repository_types());
        $this->addElement('text', ExternalRepository :: PROPERTY_TITLE, Translation :: get('ExternalRepositoryTitle'), array("size" => "50"));
        $this->addRule(ExternalRepository :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(ExternalRepository :: PROPERTY_DESCRIPTION, Translation :: get('ExternalRepositoryDescription'), true);
        $this->addElement('checkbox', ExternalRepository :: PROPERTY_ENABLED, Translation :: get('ExternalRepositoryEnabled'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', ExternalRepository :: PROPERTY_ID);
        
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

    function update_external_repository()
    {
        $external_repository = $this->external_repository;
        $values = $this->exportValues();
        
        $external_repository->set_title($values[ExternalRepository :: PROPERTY_TITLE]);
        $external_repository->set_description($values[ExternalRepository :: PROPERTY_DESCRIPTION]);
        $external_repository->set_type($values[ExternalRepository :: PROPERTY_TYPE]);
        $external_repository->set_creation_date(time());
        $external_repository->set_modification_date(time());
        
        if (isset($values[ExternalRepository :: PROPERTY_ENABLED]))
        {
            $external_repository->set_enabled(true);
        }
        else
        {
            $external_repository->set_enabled(false);
        }
        
        if (! $external_repository->update())
        {
            return false;
        }
        
        return true;
    }

    function create_external_repository()
    {
        $external_repository = $this->external_repository;
        $values = $this->exportValues();
        
        $external_repository->set_title($values[ExternalRepository :: PROPERTY_TITLE]);
        $external_repository->set_description($values[ExternalRepository :: PROPERTY_DESCRIPTION]);
        $external_repository->set_type($values[ExternalRepository :: PROPERTY_TYPE]);
        $external_repository->set_creation_date(time());
        $external_repository->set_modification_date(time());
        
        if (isset($values[ExternalRepository :: PROPERTY_ENABLED]))
        {
            $external_repository->set_enabled(true);
        }
        else
        {
            $external_repository->set_enabled(false);
        }
        
        if (! $external_repository->create())
        {
            return false;
        }
        //        
        //        $success_config = HomeDataManager :: get_instance()->create_block_properties($homeblock);
        //        
        //        if (! $success_config)
        //        {
        //            return false;
        //        }
        

        return true;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $external_repository = $this->external_repository;
        $defaults[ExternalRepository :: PROPERTY_ID] = $external_repository->get_id();
        $defaults[ExternalRepository :: PROPERTY_TITLE] = $external_repository->get_title();
        $defaults[ExternalRepository :: PROPERTY_TYPE] = $external_repository->get_type();
        $defaults[ExternalRepository :: PROPERTY_DESCRIPTION] = $external_repository->get_description();
        $defaults[ExternalRepository :: PROPERTY_ENABLED] = $external_repository->get_enabled();
        parent :: setDefaults($defaults);
    }

    function get_external_repository_types()
    {
        $path = Path :: get_application_library_path() . 'external_repository_manager/type/';
        $folders = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        
        $types = array();
        foreach ($folders as $folder)
        {
            $types[$folder] = Translation :: get(Utilities :: underscores_to_camelcase($folder));
        }
        ksort($types);
        return $types;
    }
}
?>
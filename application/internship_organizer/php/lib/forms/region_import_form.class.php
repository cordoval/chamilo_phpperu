<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'import/internship_organizer_import.class.php';

class InternshipOrganizerRegionImportForm extends FormValidator
{
    const IMPORT_FILE_NAME = 'region_file';
    
    private $region_id;
    private $user;
    private $import_type;
    private $messages;
    private $warnings;
    private $errors;

    /**
     * Constructor.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    function InternshipOrganizerRegionImportForm($form_name, $method = 'post', $action = null, $region_id, $user, $import_type = null)
    {
        parent :: __construct($form_name, $method, $action);
        $this->region_id = $region_id;
        $this->import_type = $import_type;
        $this->user = $user;
        $this->build_basic_form();
        $this->setDefaults();
    }

    /**
     * Gets the regions defined in the Internship Organizer.
     * @return array The categories.
     */
    function get_regions()
    {
        $regionmenu = new InternshipOrganizerRegionMenu($this->region_id, $this->get_user()->get_id());
        $renderer = new OptionsMenuRenderer();
        $regionmenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Builds a form to import a learning object.
     */
    private function build_basic_form()
    {
        
        $this->add_select(InternshipOrganizerRegionManager :: PARAM_REGION_ID, Translation :: get('Region'), $this->get_regions());
        
        if ($this->import_type == null)
        {
            $this->add_select('type', Translation :: get('Type'), $this->get_types());
        }
        else
        {
            $this->addElement('hidden', 'type');
        }
        
        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function get_types()
    {
        $folder = WebApplication :: get_application_class_lib_path('internship_organizer') . 'import/';
        $folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($folders as $f)
        {
            if (strpos($f, '.svn') !== false || strpos($f, 'document') !== false || strpos($f, 'zip') !== false)
            {
                continue;
            }
         
            
            $types[$f] = Translation :: get('Type' . $f);
        }
        
        return $types;
    }

    function get_messages()
    {
        return empty($this->messages) ? array() : $this->messages;
    }

    function get_warnings()
    {
        return empty($this->warnings) ? array() : $this->warnings;
    }

    function get_errors()
    {
        return empty($this->errors) ? array() : $this->errors;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults[InternshipOrganizerRegionManager :: PARAM_REGION_ID] = $this->get_region_id();
        $defaults['type'] = 'excel';
        parent :: setDefaults($defaults);
    }

    function set_values($defaults)
    {
        parent :: setDefaults($defaults);
    }

    /**
     * Imports a learning object from the submitted form values.
     * @return ContentObject The newly imported learning object.
     */
    function import_region()
    {
        $type = $this->exportValue('type');
        
        if (InternshipOrganizerImport :: type_supported($type))
        {
            $importer = InternshipOrganizerImport :: factory($type, $_FILES[self :: IMPORT_FILE_NAME], $this->get_user(), InternshipOrganizerImport :: REGION);
            
            $result = $importer->import_internship_organizer_object();
            $this->messages = $importer->get_messages();
            $this->warnings = $importer->get_warnings();
            $this->errors = $importer->get_errors();
            return $result;
        }
        else
        {
            return false;
        }
    }

    function get_region_id()
    {
        return $this->region_id;
    }

    function get_user()
    {
        return $this->user;
    }
}
?>
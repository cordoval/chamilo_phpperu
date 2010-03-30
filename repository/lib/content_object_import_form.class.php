<?php
/**
 * $Id: content_object_import_form.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */


/**
 * A form to import a ContentObject.
 */
class ContentObjectImportForm extends FormValidator
{
    const IMPORT_FILE_NAME = 'content_object_file';
    
    private $category;
    private $user;
    private $import_type;
    private $show_category;

    /**
     * Constructor.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    function ContentObjectImportForm($form_name, $method = 'post', $action = null, $category, $user, $import_type = null, $show_category = true)
    {
        parent :: __construct($form_name, $method, $action);
        $this->category = $category;
        $this->import_type = $import_type;
        $this->user = $user;
        $this->show_category = $show_category;
        $this->build_basic_form();
        $this->setDefaults();
    }

    /**
     * Gets the categories defined in the user's repository.
     * @return array The categories.
     */
    function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_user()->get_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Builds a form to import a learning object.
     */
    private function build_basic_form()
    {
        if($this->show_category)
        {
    		$this->add_select(RepositoryManager :: PARAM_CATEGORY_ID, Translation :: get('CategoryTypeName'), $this->get_categories());
        }
        else
        {
        	$this->addElement('hidden', RepositoryManager :: PARAM_CATEGORY_ID);	
        }
        
        $this->add_select('type', Translation :: get('Type'), $this->get_types());
        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
        //$this->addElement('submit', 'content_object_import', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function get_types()
    {
        $folder = dirname(__FILE__) . '/import/';
        $folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($folders as $f)
        {
            if (strpos($f, '.svn') !== false || strpos($f, 'csv') !== false)
                continue;
            
            $types[$f] = Translation :: get('Type' . $f);
        }
        
        return $types;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults[RepositoryManager :: PARAM_CATEGORY_ID] = $this->get_category();
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
    function import_content_object()
    {
        $type = $this->exportValue('type');
        
        if (ContentObjectImport :: type_supported($type))
        {
            $importer = ContentObjectImport :: factory($type, $_FILES[self :: IMPORT_FILE_NAME], $this->get_user(), $this->exportValue(RepositoryManager :: PARAM_CATEGORY_ID));
            return $importer->import_content_object();
        }
        else
        {
            return false;
        }
    }

    /**
     * Displays the form
     */
    function display()
    {
        if ($this->get_user()->get_id() == 0)
            return parent :: display();
        
        $quotamanager = new QuotaManager($this->get_user());
        if ($quotamanager->get_available_database_space() <= 0)
        {
            Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfContentObjectsReached')));
        }
        else
        {
            parent :: display();
        }
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function get_category()
    {
        return $this->category;
    }

    function get_user()
    {
        return $this->user;
    }
}
?>

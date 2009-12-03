<?php
/**
 * $Id: repository_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
/**
 * This	 installer can be used to create the storage structure for the
 * repository.
 */
class RepositoryInstaller extends Installer
{

    /**
     * Constructor
     */
    function RepositoryInstaller($values)
    {
        parent :: __construct($values, RepositoryDataManager :: get_instance());
    }

    /**
     * Runs the install-script. After creating the necessary tables to store the
     * common learning object information, this function will scan the
     * directories of all learning object types. When an XML-file describing a
     * storage unit is found, this function will parse the file and create the
     * storage unit.
     */
    function install_extra()
    {
        $rdm = $this->get_data_manager();
        $dir = dirname(__FILE__) . '/../lib/content_object';
        
        // Get the learning object xml-files if they exist
        $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);
        
        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                // Create the learning object table that stores the additional lo-properties
                if (! $this->create_storage_unit($file))
                {
                    return false;
                }
            }
        }
        
        // Register the learning objects
        $folders = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
        
        foreach ($folders as $folder)
        {
            if ($folder == '.svn')
                continue;
            
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ContentObjectRegistration') . ': <em>' . $folder . '</em>');
            
            $content_object_registration = new Registration();
            $content_object_registration->set_type(Registration :: TYPE_CONTENT_OBJECT);
            $content_object_registration->set_name($folder);
            $content_object_registration->set_status(Registration :: STATUS_ACTIVE);
            
            if (! $content_object_registration->create())
            {
                return $this->installation_failed(Translation :: get('ContentObjectRegistrationFailed'));
            }
        }
        
        if (! $this->add_metadata_catalogs())
        {
            return false;
        }
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }

    function add_metadata_catalogs()
    {
        /** LANGUAGES **/
        $languages = array(array('name' => 'Dutsch', 'value' => 'nl'), array('name' => 'English', 'value' => 'en'), array('name' => 'French', 'value' => 'fr'), array('name' => 'German', 'value' => 'de'), array('name' => 'Italian', 'value' => 'it'), array('name' => 'Spanish', 'value' => 'es'));
        
        $this->add_metadata_catalog_type(Catalog :: CATALOG_LOM_LANGUAGE, $languages);
        
        /** ROLES **/
        $roles = array(array('name' => 'author', 'value' => 'author'), array('name' => 'validator', 'value' => 'validator'), array('name' => 'unknown', 'value' => 'unknown'), array('name' => 'initiator', 'value' => 'initiator'), array('name' => 'terminator', 'value' => 'terminator'), array('name' => 'publisher', 'value' => 'publisher'), array('name' => 'editor', 'value' => 'editor'), array('name' => 'graphical_designer', 'value' => 'graphical_designer'), array('name' => 'technical_implementer', 'value' => 'technical_implementer'), array('name' => 'content_provider', 'value' => 'content_provider'), array('name' => 'technical_validator', 'value' => 'technical_validator'), array('name' => 'educational_validator', 'value' => 'educational_validator'), array('name' => 'script_writer', 'value' => 'script_writer'), array('name' => 'instructional_designer', 'value' => 'instructional_designer'), array('name' => 'subject_matter_expert', 'value' => 'subject_matter_expert'));
        
        $this->add_metadata_catalog_type(Catalog :: CATALOG_LOM_ROLE, $roles);
        
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('MetadataCatalogCreated'));
        
        return true;
    }

    function add_metadata_catalog_type($type, $data_array)
    {
        foreach ($data_array as $index => $data)
        {
            $catalogItem = new ContentObjectMetadataCatalog();
            $catalogItem->set_type($type);
            $catalogItem->set_name($data['name']);
            $catalogItem->set_value($data['value']);
            $catalogItem->set_sort($index * 10);
            
            if (! $catalogItem->save())
            {
                $this->add_message(self :: TYPE_ERROR, Translation :: get('MetadataUnableToAddCatalogItem'));
                return false;
            }
        }
    }
}
?>
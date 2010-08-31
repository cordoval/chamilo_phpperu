<?php
require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_installer.class.php';
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

        //    	$dir = dirname(__FILE__) . '/../lib/content_object';
        //        // Register the learning objects
        //        $folders = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
        //
        //        foreach ($folders as $folder)
        //        {
        //            $content_object = ContentObjectInstaller::factory($folder);
        //            if ($content_object)
        //            {
        //            	$content_object->install();
        //            	$this->add_message(Installer::TYPE_NORMAL, $content_object->retrieve_message());
        //            }
        //        }

		if (!RepositoryRights :: create_content_objects_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ContentObjectsSubtreeCreated'));
        }
        
        if (!RepositoryRights :: create_external_repositories_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ExternalRepositoriesSubtreeCreated'));
        }
        
        
        if (! $this->add_metadata_catalogs())
        {
            return false;
        }

        if (! $this->install_external_repository_managers())
        {
            return false;
        }

        return true;
    }

    //    function process_result($content_object, $result, $message, $default_collapse = true)
    //    {
    //        echo $this->display_install_block_header($content_object, $result, $default_collapse);
    //        echo $message;
    //        echo $this->display_install_block_footer();
    //        if (! $result)
    //        {
    //            $this->parent->display_footer();
    //            exit();
    //        }
    //    }
    //
    //	function display_install_block_header($content_object, $result, $default_collapse)
    //    {
    //        $counter = $this->counter;
    //
    //        $html = array();
    //        $html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(../layout/aqua/images/admin/place_' . $application . '.png);' . ($counter % 2 == 0 ? 'background-color: #fafafa;' : '') . '">';
    //        $html[] = '<div class="title">' . Translation :: get(ContentObject::type_to_class($content_object)) . '</div>';
    //
    //        $collapse = '';
    //
    //        if($result && $default_collapse)
    //        {
    //        	$collapse = ' collapse';
    //        }
    //
    //        $html[] = '<div class="description' . $collapse . '">';
    //
    //        return implode("\n", $html);
    //    }
    //
    //    function display_install_block_footer()
    //    {
    //        $html = array();
    //        $html[] = '</div>';
    //        $html[] = '</div>';
    //        return implode("\n", $html);
    //    }


    function get_path()
    {
        return dirname(__FILE__);
    }

    function add_metadata_catalogs()
    {
        /** LANGUAGES **/
        $languages = array(
                array('name' => 'Dutsch', 'value' => 'nl'), array('name' => 'English', 'value' => 'en'), array('name' => 'French', 'value' => 'fr'), array('name' => 'German', 'value' => 'de'), array('name' => 'Italian', 'value' => 'it'),
                array('name' => 'Spanish', 'value' => 'es'));

        $this->add_metadata_catalog_type(Catalog :: CATALOG_LOM_LANGUAGE, $languages);

        /** ROLES **/
        $roles = array(
                array('name' => 'author', 'value' => 'author'), array('name' => 'validator', 'value' => 'validator'), array('name' => 'unknown', 'value' => 'unknown'), array('name' => 'initiator', 'value' => 'initiator'),
                array('name' => 'terminator', 'value' => 'terminator'), array('name' => 'publisher', 'value' => 'publisher'), array('name' => 'editor', 'value' => 'editor'),
                array('name' => 'graphical_designer', 'value' => 'graphical_designer'), array('name' => 'technical_implementer', 'value' => 'technical_implementer'), array('name' => 'content_provider', 'value' => 'content_provider'),
                array('name' => 'technical_validator', 'value' => 'technical_validator'), array('name' => 'educational_validator', 'value' => 'educational_validator'), array('name' => 'script_writer', 'value' => 'script_writer'),
                array('name' => 'instructional_designer', 'value' => 'instructional_designer'), array('name' => 'subject_matter_expert', 'value' => 'subject_matter_expert'));

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

    function install_external_repository_managers()
    {
        // Adding the YouTube Manager
        $youtube = new ExternalRepository();
        $youtube->set_title('YouTube');
        $youtube->set_type('youtube');
        $youtube->set_description(Translation :: get('YouTubeTagline'));
        $youtube->set_enabled(true);
        $youtube->set_creation_date(time());
        $youtube->set_modification_date(time());
        if (! $youtube->create())
        {
            $this->add_message(self :: TYPE_ERROR, Translation :: get('ExternalRepositoryManagerNotAdded') . ': YouTube');
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ExternalRepositoryManagerAdded') . ': YouTube');
        }

        // Adding the Flickr Manager
        $flickr = new ExternalRepository();
        $flickr->set_title('Flickr');
        $flickr->set_type('flickr');
        $flickr->set_description(Translation :: get('FlickrTagline'));
        $flickr->set_enabled(true);
        $flickr->set_creation_date(time());
        $flickr->set_modification_date(time());
        if (! $flickr->create())
        {
            $this->add_message(self :: TYPE_ERROR, Translation :: get('ExternalRepositoryManagerNotAdded') . ': Flickr');
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ExternalRepositoryManagerAdded') . ': Flickr');
        }
        
    	// Adding the Matterhorn Manager
        $matterhorn = new ExternalRepository();
        $matterhorn->set_title('Matterhorn');
        $matterhorn->set_type('matterhorn');
        $matterhorn->set_description(Translation :: get('MatterhornTagline'));
        $matterhorn->set_enabled(true);
        $matterhorn->set_creation_date(time());
        $matterhorn->set_modification_date(time());
        if (! $matterhorn->create())
        {
            $this->add_message(self :: TYPE_ERROR, Translation :: get('ExternalRepositoryManagerNotAdded') . ': Matterhorn');
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ExternalRepositoryManagerAdded') . ': Matterhorn');
        }
        
        return true;
    }
}
?>
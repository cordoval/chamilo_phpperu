<?php
require_once dirname(__FILE__) . '/photobucket_external_repository_connector.class.php';
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_browser_gallery_table_cell_renderer.class.php';
/**
 * 
 * @author magali.gillard
 *
 */
class PhotobucketExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'photobucket';

    const PARAM_MEDIAFILE = 'mediafile_id';

    /**
     * @param Application $application
     */
    function PhotobucketExternalRepositoryManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/photobucket/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings()
    {
//  		$developer_key = ExternalRepositorySetting :: get('developer_key');
//
//        if (! $developer_key)
//        {
//            return false;
//        }
        return true;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#support_sorting_direction()
     */
    function support_sorting_direction()
    {
        return true;
    }

    /**
     * @param ExternalRepositoryObject $object
     * @return string
     */
    function get_external_repository_object_viewing_url(ExternalRepositoryObject $object)
    {
        $parameters = array();
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_menu_items()
     */
    function get_menu_items()
    {
        $menu_items = array();
        
        return $menu_items;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#is_ready_to_be_used()
     */
    function is_ready_to_be_used()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
    function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY, self :: ACTION_EXPORT_EXTERNAL_REPOSITORY);

        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalRepositorySetting :: get_all()) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        return $actions;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_available_renderers()
     */
    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW, ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_content_object_type_conditions()
     */
    function get_content_object_type_conditions()
    {
        $video_types = Document :: get_video_types();
        $video_conditions = array();
        foreach ($video_types as $video_type)
        {
            $video_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $video_type, Document :: get_type_name());
        }

        return new OrCondition($video_conditions);
    }

    /**
     * @return string
     */
    function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }
    
   /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION;
    }
}
?>
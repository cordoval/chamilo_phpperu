<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\ActionBarSearchForm;
use repository\content_object\document\Document;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\extensions\external_repository_manager\ExternalRepositoryObjectRenderer;
use common\extensions\external_repository_manager\ExternalRepositoryObjectDisplay;

use repository\ExternalSetting;

class DropboxExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'dropbox';
    
    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';

   	const FEED_TYPE_GENERAL = 1;
   	const FEED_TYPE_PHOTOS = 2;
   	const FEED_TYPE_PUBLIC = 3;    

    /**
     * @param Application $application
     */
    function DropboxExternalRepositoryManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'external_repository_manager/implementation/dropbox/php/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings($external_repository)
    {
//    	$key = ExternalSetting :: get('key');
//        $secret = ExternalSetting :: get('secret');
//
//        if (! $key || ! $secret)
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

        $general = array();
        $general['title'] = Translation :: get('Dropbox');
        $general['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        $menu_items[] = $general;

        $folders = $this->get_external_repository_connector()->retrieve_folders($this->get_url(array(self :: PARAM_FOLDER => '__PLACEHOLDER__')));
        $menu_items = array_merge($menu_items, $folders);
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
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY, self ::ACTION_EXPORT_EXTERNAL_REPOSITORY);

        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalSetting :: get_all($this->get_external_repository()->get_id())) > 0);

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
        return array(ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_content_object_type_conditions()
     */
    function get_content_object_type_conditions()
    {
        /*$file_types = Document :: get_file_types();
        $image_conditions = array();
        foreach ($file_types as $image_type)
        {
            $file_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $file_type, Document :: get_type_name());
        }
        return new OrCondition($image_conditions);*/
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
<?php
require_once dirname(__FILE__) . '/picasa_external_repository_connector.class.php';

/**
 * @author Hans De Bisschop
 */
class PicasaExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'picasa';

    const PARAM_FEED_TYPE = 'feed';

    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MOST_INTERESTING = 2;
    const FEED_TYPE_MOST_RECENT = 3;
    const FEED_TYPE_MY_PHOTOS = 4;

    /**
     * @param Application $application
     */
    function PicasaExternalRepositoryManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/picasa/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings()
    {
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
        $general['title'] = Translation :: get('Browse');
        $general['url'] = $this->get_url(array(self :: PARAM_FOLDER => null), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        $menu_items[] = $general;

        $my_photos = array();
        $my_photos['title'] = Translation :: get('MyPhotos');
        $my_photos['url'] = $this->get_url(array(self :: PARAM_FOLDER => PicasaExternalRepositoryConnector :: PHOTOS_MINE), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $my_photos['class'] = 'user';
        $menu_items[] = $my_photos;

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
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY);
        //$actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY, self :: ACTION_EXPORT_EXTERNAL_REPOSITORY);

        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalRepositorySetting :: get_all()) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        return $actions;
    }

    function run()
    {
        $parent = $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);

        switch ($parent)
        {
            case ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Browser', $this);
                break;
            case ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Viewer', $this);
                break;
            case ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Importer', $this);
                break;
            case ExternalRepositoryManager :: ACTION_UPLOAD_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Uploader', $this);
                break;
            case ExternalRepositoryManager :: ACTION_EDIT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Editor', $this);
                break;
            case ExternalRepositoryManager :: ACTION_DELETE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Deleter', $this);
                break;
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
                break;
        }

        $component->run();
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
        $image_types = Document :: get_image_types();
        $image_conditions = array();
        foreach ($image_types as $image_type)
        {
            $image_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $image_type, Document :: get_type_name());
        }

        return new OrCondition($image_conditions);
    }

    /**
     * @return string
     */
    function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }
}
?>
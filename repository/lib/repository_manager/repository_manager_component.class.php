<?php
/**
 * $Id: repository_manager_component.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager
 */
/**
 * Base class for a repository manager component.
 * A repository manager provides different tools to the end user. Each tool is
 * represented by a repository manager component and should extend this class.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class RepositoryManagerComponent extends CoreApplicationComponent
{

    function display_header($breadcrumbtrail, $display_search = false, $display_menu = true, $helpitem)
    {
        $this->get_parent()->display_header($breadcrumbtrail, $display_search, $display_menu, $helpitem);
    }

    /**
     * Constructor
     * @param RepositoryManager $repository_manager The repository manager which
     * provides this component
     */
    protected function RepositoryManagerComponent($repository_manager)
    {
        parent :: __construct($repository_manager);
    }

    /**
     * @see RepositoryManager::get_parameters()
     */
    function get_parameters($include_search = false)
    {
        return $this->get_parent()->get_parameters($include_search);
    }

    /**
     * @see RepositoryManager::get_search_parameter()
     */
    function get_search_parameter($name)
    {
        return $this->get_parent()->get_search_parameter($name);
    }

    /**
     * @see RepositoryManager::force_menu_url()
     */
    function force_menu_url($url)
    {
        return $this->get_parent()->force_menu_url($url);
    }

    /**
     * @see RepositoryManager::get_quota_url()
     */
    function get_quota_url()
    {
        return $this->get_parent()->get_quota_url();
    }

    /**
     * @see RepositoryManager::get_content_object_creation_url()
     */
    function get_content_object_creation_url()
    {
        return $this->get_parent()->get_content_object_creation_url();
    }

    /**
     * @see RepositoryManager::get_recycle_bin_url()
     */
    function get_recycle_bin_url()
    {
        return $this->get_parent()->get_recycle_bin_url();
    }

    /**
     * @see RepositoryManager::get_root_category_id()
     */
    function get_root_category_id()
    {
        return $this->get_parent()->get_root_category_id();
    }

    /**
     * @see RepositoryManager::retrieve_content_object()
     */
    function retrieve_content_object($id, $type = null)
    {
        return $this->get_parent()->retrieve_content_object($id, $type);
    }

    /**
     * @see RepositoryManager::retrieve_content_objects()
     */
    function retrieve_content_objects($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_content_objects($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see RepositoryManager::retrieve_type_content_objects()
     */
    function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_type_content_objects($type, $condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see RepositoryManager::get_version_ids()
     */
    function get_version_ids($object)
    {
        return $this->get_parent()->get_version_ids($object);
    }

    /**
     * @see RepositoryManager::count_content_objects()
     */
    function count_content_objects($condition = null)
    {
        return $this->get_parent()->count_content_objects($condition);
    }

    /**
     * @see RepositoryManager::count_type_content_objects()
     */
    function count_type_content_objects($type, $condition = null)
    {
        return $this->get_parent()->count_type_content_objects($type, $condition);
    }

    /**
     * @see RepositoryManager::count_content_objects()
     */
    function count_publication_attributes($user, $type = null, $condition = null)
    {
        return $this->get_parent()->count_publication_attributes($user, $type, $condition);
    }

    /**
     * Gets the number of categories the user has defined in his repository
     * @todo This function should probably move to repositorymanager
     * @return int
     */
    function get_number_of_categories()
    {
        if (! isset($this->number_of_categories))
        {
            $this->number_of_categories = RepositoryDataManager :: get_number_of_categories($this->get_user_id());
        }
        return $this->number_of_categories;

    }

    /**
     * @see RepositoryManager::content_object_deletion_allowed()
     */
    function content_object_deletion_allowed($content_object, $type = null)
    {
        return $this->get_parent()->content_object_deletion_allowed($content_object, $type);
    }

    /**
     * @see RepositoryManager::content_object_revert_allowed()
     */
    function content_object_revert_allowed($content_object)
    {
        return $this->get_parent()->content_object_revert_allowed($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_publication_attributes()
     */
    function get_content_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->get_content_object_publication_attributes($user, $id, $type, $offset, $count, $order_property);
    }

    /**
     * @see RepositoryManager::get_content_object_publication_attribute()
     */
    function get_content_object_publication_attribute($id, $type = null, $application)
    {
        return $this->get_parent()->get_content_object_publication_attribute($id, $application);
    }

    /**
     * @see RepositoryManager::get_publication_update_url()
     */
    function get_publication_update_url($content_object)
    {
        return $this->get_parent()->get_publication_update_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_viewing_url()
     */
    function get_content_object_viewing_url($content_object)
    {
        return $this->get_parent()->get_content_object_viewing_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_delete_publications_url()
     */
    function get_content_object_delete_publications_url($content_object)
    {
        return $this->get_parent()->get_content_object_delete_publications_url($content_object);
    }

	function get_content_object_unlinker_url($content_object)
    {
    	return $this->get_parent()->get_content_object_unlinker_url($content_object);
    }
    
    /**
     * @see RepositoryManager::get_content_object_editing_url()
     */
    function get_content_object_editing_url($content_object)
    {
        return $this->get_parent()->get_content_object_editing_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_recycling_url()
     */
    function get_content_object_recycling_url($content_object)
    {
        return $this->get_parent()->get_content_object_recycling_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_restoring_url()
     */
    function get_content_object_restoring_url($content_object)
    {
        return $this->get_parent()->get_content_object_restoring_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_deletion_url()
     */
    function get_content_object_deletion_url($content_object, $type = null)
    {
        return $this->get_parent()->get_content_object_deletion_url($content_object, $type);
    }

    /**
     * @see RepositoryManager::get_content_object_revert_url()
     */
    function get_content_object_revert_url($content_object)
    {
        return $this->get_parent()->get_content_object_revert_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_moving_url()
     */
    function get_content_object_moving_url($content_object)
    {
        return $this->get_parent()->get_content_object_moving_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_metadata_editing_url()
     */
    function get_content_object_metadata_editing_url($content_object)
    {
        return $this->get_parent()->get_content_object_metadata_editing_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_rights_editing_url()
     */
    function get_content_object_rights_editing_url($content_object)
    {
        return $this->get_parent()->get_content_object_rights_editing_url($content_object);
    }

    /**
     * @see RepositoryManager::get_content_object_types()
     */
    function get_content_object_types($only_master_types = false)
    {
        return $this->get_parent()->get_content_object_types($only_master_types);
    }

    /**
     * @see RepositoryManager::get_user_info()
     */
    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    function get_registered_types($only_master_types = false)
    {
        return $this->get_parent()->get_registered_types($only_master_types);
    }

    /**
     * @see RepositoryManager::get_type_filter_url()
     */
    function get_type_filter_url($type)
    {
        return $this->get_parent()->get_type_filter_url($type);
    }

    /**
     * @see RepositoryManager::get_search_condition()
     */
    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    /**
     * @see RepositoryManager::get_category_condition()
     */
    function get_category_condition($category_id)
    {
        return $this->get_parent()->get_category_condition($category_id);
    }

    function count_complex_content_object_items($condition)
    {
        return $this->get_parent()->count_complex_content_object_items($condition);
    }

    function retrieve_complex_content_object_items($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_complex_content_object_items($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_complex_content_object_item($cloi_id)
    {
        return $this->get_parent()->retrieve_complex_content_object_item($cloi_id);
    }

    function get_complex_content_object_item_edit_url($cloi, $root_id)
    {
        return $this->get_parent()->get_complex_content_object_item_edit_url($cloi, $root_id);
    }

    function get_complex_content_object_item_delete_url($cloi, $root_id)
    {
        return $this->get_parent()->get_complex_content_object_item_delete_url($cloi, $root_id);
    }

    function get_complex_content_object_item_move_url($cloi, $root_id, $direction)
    {
        return $this->get_parent()->get_complex_content_object_item_move_url($cloi, $root_id, $direction);
    }

    function get_browse_complex_content_object_url($object)
    {
        return $this->get_parent()->get_browse_complex_content_object_url($object);
    }

    function get_add_existing_content_object_url($root_id, $clo_id)
    {
        return $this->get_parent()->get_add_existing_content_object_url($root_id, $clo_id);
    }

    function get_add_content_object_url($content_object, $cloi_id, $root_id)
    {
        return $this->get_parent()->get_add_content_object_url($content_object, $cloi_id, $root_id);
    }

    function get_content_object_exporting_url($content_object)
    {
        return $this->get_parent()->get_content_object_exporting_url($content_object);
    }

    function get_publish_content_object_url($content_object)
    {
        return $this->get_parent()->get_publish_content_object_url($content_object);
    }

    function count_categories($conditions = null)
    {
        return $this->get_parent()->count_categories($conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_browse_user_views_url()
    {
        return $this->get_parent()->get_browse_user_views_url();
    }

    function get_create_user_view_url()
    {
        return $this->get_parent()->create_user_view_url();
    }

    function get_update_user_view_url($user_view_id)
    {
        return $this->get_parent()->update_user_view_url($user_view_id);
    }

    function get_delete_user_view_url($user_view_id)
    {
        return $this->get_parent()->delete_user_view_url($user_view_id);
    }

    function count_user_views($conditions = null)
    {
        return $this->get_parent()->count_user_views($conditions);
    }

    function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_user_views($condition, $offset, $count, $order_property);
    }

    function get_copy_content_object_url($lo_id, $to_user_id)
    {
        return $this->get_parent()->get_copy_content_object_url($lo_id, $to_user_id);
    }

    function get_delete_template_url($template_id)
    {
        return $this->get_parent()->get_delete_template_url($template_id);
    }
    
	function get_delete_link_url($type, $object_id, $link_id)
    {
    	return $this->get_parent()->get_delete_link_url($type, $object_id, $link_id);
    }
    
    function get_document_downloader_url($document_id)
    {
    	return $this->get_parent()->get_document_downloader_url($document_id);
    }
}

?>
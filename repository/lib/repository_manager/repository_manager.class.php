<?php
/**
 * @package repository.lib.repository_manager
 *
 * A repository manager provides some functionalities to the end user to manage
 * his learning objects in the repository. For each functionality a component is
 * available.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManager extends CoreApplication
{
    const APPLICATION_NAME = 'repository';

    /**#@+
     * Constant defining a parameter of the repository manager.
     */
    // SortableTable hogs 'action' so we'll use something else.
    const PARAM_CATEGORY_ID = 'category';
    const PARAM_CONTENT_OBJECT_ID = 'object';
    const PARAM_DESTINATION_CONTENT_OBJECT_ID = 'destination';
    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PARAM_DELETE_PERMANENTLY = 'delete_permanently';
    const PARAM_DELETE_VERSION = 'delete_version';
    const PARAM_DELETE_RECYCLED = 'delete_recycle';
    const PARAM_EXPORT_SELECTED = 'export_selected';
    const PARAM_EXPORT_CP_SELECTED = 'export_cp_selected';
    const PARAM_EMPTY_RECYCLE_BIN = 'empty';
    const PARAM_RECYCLE_SELECTED = 'recycle_selected';
    const PARAM_MOVE_SELECTED = 'move_selected';
    const PARAM_RESTORE_SELECTED = 'restore_selected';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_EDIT_SELECTED_RIGHTS = 'rights_selected';
    const PARAM_PUBLISH_SELECTED = 'publish_selected';
    const PARAM_COMPARE_OBJECT = 'object';
    const PARAM_COMPARE_VERSION = 'compare';
    const PARAM_PUBLICATION_APPLICATION = 'publication_application';
    const PARAM_PUBLICATION_ID = 'publication';
    const PARAM_CLOI_REF = 'cloi_ref';
    const PARAM_CLOI_ID = 'cloi_id';
    const PARAM_CLOI_ROOT_ID = 'cloi_root_id';
    const PARAM_CLOI_COMPLEX_REF = 'cloi_complex_ref';
    const PARAM_DISPLAY_ORDER = 'display_order';
    const PARAM_REMOVE_SELECTED_CLOI = 'cloi_delete_selected';
    const PARAM_MOVE_DIRECTION = 'move_direction';
    const PARAM_DIRECTION_UP = 'up';
    const PARAM_DIRECTION_DOWN = 'down';
    const PARAM_ADD_OBJECTS = 'add_objects';
    const PARAM_DELETE_SELECTED_USER_VIEW = 'delete_user_view';
    const PARAM_TARGET_USER = 'target_user';
    const PARAM_TARGET_GROUP = 'target_group';
    const PARAM_DELETE_TEMPLATES = 'delete_templates';
    const PARAM_COPY_FROM_TEMPLATES = 'copy_template';
    const PARAM_COPY_TO_TEMPLATES = 'copy_to_template';
    const PARAM_EXTERNAL_OBJECT_ID = 'external_object_id';
    const PARAM_EXTERNAL_REPOSITORY_ID = 'ext_rep_id';
    const PARAM_LINK_TYPE = 'link_type';
    const PARAM_LINK_ID = 'link_id';
    const PARAM_CONTENT_OBJECT_MANAGER_TYPE = 'manage';
    const PARAM_SHOW_OBJECTS_SHARED_BY_ME = 'show_my_objects';

    const PARAM_TYPE = 'type';
    const PARAM_IDENTIFIER = 'identifier';

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_BROWSE_CONTENT_OBJECTS = 'browser';
    const ACTION_BROWSE_SHARED_CONTENT_OBJECTS = 'shared_content_objects_browser';
    const ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS = 'recycle_bin_browser';
    const ACTION_VIEW_CONTENT_OBJECTS = 'viewer';
    const ACTION_CREATE_CONTENT_OBJECTS = 'creator';
    const ACTION_EDIT_CONTENT_OBJECTS = 'editor';
    const ACTION_REVERT_CONTENT_OBJECTS = 'reverter';
    const ACTION_DELETE_CONTENT_OBJECTS = 'deleter';
    const ACTION_DELETE_CONTENT_OBJECT_PUBLICATIONS = 'publication_deleter';
    const ACTION_UNLINK_CONTENT_OBJECTS = 'unlinker';
    const ACTION_RESTORE_CONTENT_OBJECTS = 'restorer';
    const ACTION_MOVE_CONTENT_OBJECTS = 'mover';
    const ACTION_EDIT_CONTENT_OBJECT_METADATA = 'metadata_editor';
    const ACTION_VIEW_CONTENT_OBJECT_METADATA = 'metadata_viewer';
    const ACTION_EDIT_CONTENT_OBJECT_RIGHTS = 'rights_editor';
    const ACTION_VIEW_MY_PUBLICATIONS = 'publication_browser';
    const ACTION_VIEW_QUOTA = 'quota_viewer';
    const ACTION_COMPARE_CONTENT_OBJECTS = 'comparer';
    const ACTION_UPDATE_CONTENT_OBJECT_PUBLICATION = 'publication_updater';
    const ACTION_EXPORT_CONTENT_OBJECTS = 'exporter';
    const ACTION_IMPORT_CONTENT_OBJECTS = 'importer';
    const ACTION_PUBLISH_CONTENT_OBJECT = 'publisher';
    const ACTION_MANAGE_CATEGORIES = 'category_manager';
    const ACTION_EXPORT_CP_CONTENT_OBJECTS = 'export_cp';
    const ACTION_VIEW_ATTACHMENT = 'view_attachment';
    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'complex_builder';
    const ACTION_VIEW_REPO = 'attachment_viewer';
    const ACTION_DOWNLOAD_DOCUMENT = 'document_downloader';
//    const ACTION_EXTERNAL_REPOSITORY_BROWSE = 'ext_rep_browse';
//    const ACTION_EXTERNAL_REPOSITORY_EXPORT = 'ext_rep_export';
//    const ACTION_EXTERNAL_REPOSITORY_IMPORT = 'ext_rep_import';
//    const ACTION_EXTERNAL_REPOSITORY_LIST_OBJECTS = 'ext_rep_list_objects';
//    const ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW = 'ext_rep_metadata_review';
//    const ACTION_EXTERNAL_REPOSITORY_CATALOG = 'ext_rep_catalog';
    const ACTION_BROWSE_TEMPLATES = 'template_browser';
    const ACTION_COPY_CONTENT_OBJECT = 'content_object_copier';
    const ACTION_COPY_CONTENT_OBJECT_TO_TEMPLATES = 'template_creator';
    const ACTION_COPY_CONTENT_OBJECT_FROM_TEMPLATES = 'template_user';
    const ACTION_MANAGE_CONTENT_OBJECT = 'content_object_manager';
    const ACTION_MANAGE_CONTENT_OBJECT_REGISTRATIONS = 'content_object_registration_browser';
    const ACTION_RECYCLE_CONTENT_OBJECTS = 'recycler';
    const ACTION_DELETE_CONTENT_OBJECTS_PERMANENTLY = 'permanent_deleter';

    const ACTION_IMPORT_TEMPLATE = 'template_importer';
    const ACTION_DELETE_TEMPLATE = 'template_deleter';
    const ACTION_DELETE_LINK = 'link_deleter';
    const ACTION_VIEW_DOUBLES = 'doubles_viewer';
    const ACTION_EXTERNAL_REPOSITORY_MANAGER = 'external_repository';
    const ACTION_MANAGE_EXTERNAL_REPOSITORY_INSTANCES = 'external_repository_instance_manager';

    const ACTION_BROWSE_USER_VIEWS = 'user_view_browser';
    const ACTION_CREATE_USER_VIEW = 'user_view_creator';
    const ACTION_DELETE_USER_VIEW = 'user_view_deleter';
    const ACTION_UPDATE_USER_VIEW = 'user_view_updater';

    const ACTION_CONTENT_OBJECT_SHARE_BROWSER = 'content_object_share_rights_browser';
    const ACTION_CONTENT_OBJECT_SHARE_CREATOR = 'content_object_share_rights_creator';
    const ACTION_CONTENT_OBJECT_SHARE_EDITOR = 'content_object_share_rights_editor';
    const ACTION_CONTENT_OBJECT_SHARE_DELETER = 'content_object_share_rights_deleter';
    
    const ACTION_EDIT_CONTENT_OBJECT_SHARE_RIGHTS = 'content_object_share_rights_browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_CONTENT_OBJECTS;

    const PARAM_USER_VIEW = 'user_view';
    const PARAM_RENDERER = 'renderer';

    /**
     * Property of this repository manager.
     */
    private $search_parameters;
    private $search_form;
    private $category_menu;

    /**
     * Constructor
     * @param int $user_id The user id of current user
     */
    function RepositoryManager($user)
    {
        parent :: __construct($user);
        $this->determine_search_settings();
        $this->set_optional_parameters();
    }

    function set_optional_parameters()
    {
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
    }

    /**
            case self :: ACTION_EXPORT_CP_CONTENT_OBJECTS :
                $component = $this->create_component('ExporterCp');
                break;
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    break;
                case self :: PARAM_EXPORT_CP_SELECTED :
                    $this->set_action(self :: ACTION_EXPORT_CP_CONTENT_OBJECTS);
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail = null, $display_search = false, $display_menu = true)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        }

        if ($display_menu)
        {
            if (Request :: get('category'))
                $this->get_category_menu()->get_breadcrumbs(false);
        }

        $title = $breadcrumbtrail->get_last()->get_name();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        Display :: header($breadcrumbtrail);

        if ($display_menu)
        {
            echo '<div id="repository_tree_container" style="float: left; width: 15%;">';
            $this->display_content_object_categories();
            echo '</div>';
            echo '<div style="float: right; width: 82%;">';
        }
        else
        {
            echo '<div>';
        }

        echo '<div class="clear">&nbsp;</div>';

        $message = Request :: get(self :: PARAM_MESSAGE);
        if ($message)
        {
            $this->display_message($message);
        }

        $message = Request :: get(self :: PARAM_ERROR_MESSAGE);
        if ($message)
        {
            $this->display_error_message($message);
        }

        $message = Request :: get(self :: PARAM_WARNING_MESSAGE);
        if ($message)
        {
            $this->display_warning_message($message);
        }
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    /**
     * Gets the parameter list
     * @param boolean $include_search Include the search parameters in the
     * returned list?
     * @return array The list of parameters.
     */
    function get_parameters($include_search = false)
    {
        if ($include_search && isset($this->search_parameters))
        {
            return array_merge($this->search_parameters, parent :: get_parameters());
        }

        return parent :: get_parameters();
    }

    /**
     * Gets the value of a search parameter.
     * @param string $name The search parameter name.
     * @return string The search parameter value.
     */
    function get_search_parameter($name)
    {
        return $this->search_parameters[$name];
    }

    /**
     * Sets the active URL in the navigation menu.
     * @param string $url The active URL.
     */
    function force_menu_url($url)
    {
        $this->get_category_menu()->forceCurrentUrl($url);
    }

    /**
     * Gets the URL to the quota page.
     * @return string The URL.
     */
    function get_quota_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_QUOTA, self :: PARAM_CATEGORY_ID => null));
    }

    /**
     * Gets the URL to the publication page.
     * @return string The URL.
     */
    function get_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MY_PUBLICATIONS), array(), false);
    }

    /**
     * Gets the URL to the learning object creation page.
     * @return string The URL.
     */
    function get_content_object_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECTS));
    }

    /**
     * Gets the URL to the learning object import page.
     * @return string The URL.
     */
    function get_content_object_importing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_CONTENT_OBJECTS));
    }

    /**
     * Gets the URL to the recycle bin.
     * @return string The URL.
     */
    function get_recycle_bin_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS, self :: PARAM_CATEGORY_ID => null));
    }

    /**
     * Gets the id of the root category.
     * @return integer The requested id.
     */
    function get_root_category_id()
    {
        /*if (isset ($this->category_menu))
		{
			return $this->category_menu->_menu[0][OptionsMenuRenderer :: KEY_ID];
		}
		else
		{
			$dm = RepositoryDataManager :: get_instance();
			$cat = $dm->retrieve_root_category($this->get_user_id());
			return $cat->get_id();
		}*/        return 0;
    }

    /**
     * Retrieves a learning object.
     * @param int $id The id of the learning object.
     * @param string $type The type of the learning object. Default is null. If
     * you know the type of the requested object, you should give it as a
     * parameter as this will make object retrieval faster.
     */
    function retrieve_content_object($id, $type = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($id, $type);
    }

    /**
     * @see RepositoryDataManager::retrieve_content_objects()
     */
    function retrieve_content_objects($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_content_object_versions_resultset($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object_versions_resultset($condition, $order_by, $offset, $max_objects);
    }

    function count_content_object_versions_resultset($condition = null)
    {
        return RepositoryDataManager :: get_instance()->count_content_object_versions_resultset($condition);
    }

    /**
     * @see RepositoryDataManager::retrieve_type_content_objects()
     */
    function retrieve_type_content_objects($type, $condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return RepositoryDataManager :: get_instance()->retrieve_type_content_objects($type, $condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see RepositoryDataManager::get_version_ids()
     */
    function get_version_ids($object)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->get_version_ids($object);
    }

    /**
     * @see RepositoryDataManager::count_content_objects()
     */
    function count_content_objects($condition = null)
    {
        return RepositoryDataManager :: get_instance()->count_content_objects($condition);
    }

    /**
     * @see RepositoryDataManager::count_type_content_objects()
     */
    function count_type_content_objects($type, $condition = null)
    {
        return RepositoryDataManager :: get_instance()->count_type_content_objects($type, $condition);
    }

    function count_publication_attributes($user, $type = null, $condition = null)
    {
        return RepositoryDataManager :: count_publication_attributes($user, $type, $condition);
    }

    /**
     * @see RepositoryDataManager::content_object_deletion_allowed()
     */
    function content_object_deletion_allowed($content_object, $type = null)
    {
        return RepositoryDataManager :: content_object_deletion_allowed($content_object, $type);
    }

    /**
     * @see RepositoryDataManager::content_object_revert_allowed()
     */
    function content_object_revert_allowed($content_object)
    {
        return RepositoryDataManager :: content_object_revert_allowed($content_object);
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attributes()
     */
    function get_registered_types()
    {
        return RepositoryDataManager :: get_registered_types();
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attributes()
     */
    function get_content_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return RepositoryDataManager :: get_content_object_publication_attributes($user, $id, $type, $offset, $count, $order_property);
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attribute()
     */
    function get_content_object_publication_attribute($id, $application)
    {
        return RepositoryDataManager :: get_content_object_publication_attribute($id, $application);
    }

    function get_publication_update_url($publication_attribute)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CONTENT_OBJECT_PUBLICATION, self :: PARAM_PUBLICATION_APPLICATION => $publication_attribute->get_application(), self :: PARAM_PUBLICATION_ID => $publication_attribute->get_id()));
    }

    /**
     * Gets the url to view a learning object.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_viewing_url($content_object)
    {
        if ($content_object->get_state() == ContentObject :: STATE_RECYCLED)
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), self :: PARAM_CATEGORY_ID => null));
        }
        if ($content_object->get_type() == 'category')
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS, self :: PARAM_CATEGORY_ID => $content_object->get_id()));
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), self :: PARAM_CATEGORY_ID => $content_object->get_parent_id()));
    }

    function get_external_repository_object_viewing_url(ExternalRepositorySync $external_repository_sync)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_EXTERNAL_REPOSITORY_MANAGER, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY => $external_repository_sync->get_external_repository_id(),
                ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY,
                ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $external_repository_sync->get_external_repository_object_id()));
    }

    /**
     * Gets the url to view a learning object.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_editing_url($content_object)
    {
        if (! $content_object->is_latest_version())
        {
            return null;
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to delete a learning object's publications.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_delete_publications_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECT_PUBLICATIONS, self :: PARAM_PUBLICATION_ID => $content_object->get_id(), self :: PARAM_PUBLICATION_APPLICATION => $content_object->get_application()));
    }

    function get_content_object_unlinker_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNLINK_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to recycle a learning object (move the object to the
     * recycle bin).
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_recycling_url($content_object, $force = false)
    {
        if (! $this->content_object_deletion_allowed($content_object) || $content_object->get_state() == ContentObject :: STATE_RECYCLED)
        {
            return null;
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), self :: PARAM_DELETE_RECYCLED => 1));
    }

    /**
     * Gets the url to restore a learning object from the recycle bin.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_restoring_url($content_object)
    {
        if ($content_object->get_state() != ContentObject :: STATE_RECYCLED)
        {
            return null;
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RESTORE_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to delete a learning object from recycle bin.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_deletion_url($content_object, $type = null)
    {
        if (! $this->content_object_deletion_allowed($content_object, $type))
        {
            return null;
        }

        if (isset($type))
        {
            $param = self :: PARAM_DELETE_VERSION;
        }
        else
        {
            if ($content_object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $param = self :: PARAM_DELETE_PERMANENTLY;
            }
            else
            {
                $param = self :: PARAM_DELETE_RECYCLED;
            }
        }
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), $param => 1));
    }

    /**
     * Gets the url to revert to a learning object version.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_revert_url($content_object)
    {
        if (! $this->content_object_revert_allowed($content_object))
        {
            return null;
        }

        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REVERT_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to move a learning object to another category.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_moving_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to edit the metadata of a learning object.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_metadata_editing_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_METADATA, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the url to edit the rights on a learning object.
     * @param ContentObject $content_object The learning object.
     * @return string The requested URL.
     */
    function get_content_object_rights_editing_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, self :: PARAM_IDENTIFIER => $content_object->get_id(), self :: PARAM_TYPE => RepositoryRights :: TYPE_USER_CONTENT_OBJECT));
    }

    /**
     * Gets the url to edit the rights on a learning object type.
     * @param String $content_object_type The learning object.
     * @return string The requested URL.
     */
    function get_content_object_type_rights_editing_url($registration)
    {
        if ($registration)
        {
            $id = $registration->get_id();
        }

        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, self :: PARAM_IDENTIFIER => $id, self :: PARAM_TYPE => RepositoryRights :: TYPE_CONTENT_OBJECT));
    }

    /**
     * Gets the defined learning object types
     * @see RepositoryDataManager::get_registered_types()
     * @param boolean $only_master_types Only return the master type learning
     * objects (which can exist on their own). Returns all learning object types
     * by default.
     */
    function get_content_object_types($check_view_right = true)
    {
        return RepositoryDataManager :: get_registered_types($check_view_right);
    }

    /**
     * Gets some user information
     * @param int $id The user id
     * @return The user
     */
    function get_user_info($user_id)
    {
        return UserDataManager :: get_instance()->retrieve_user($user_id);
    }

    /**
     * Gets the url for browsing objects of a given type
     * @param string $type The requested type
     * @return string The url
     */
    function get_type_filter_url($type)
    {
        $params = array();
        $params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_CONTENT_OBJECTS;
        $params[self :: PARAM_CONTENT_OBJECT_TYPE] = array($type);
        return $this->get_url($params);
    }

    function get_content_object_manager_url($content_object_type, $manager)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CONTENT_OBJECT, self :: PARAM_CONTENT_OBJECT_TYPE => $content_object_type, self :: PARAM_CONTENT_OBJECT_MANAGER_TYPE => $manager));
    }

    /**
     * @see RepositorySearchForm::get_condition()
     */
    function get_search_condition()
    {
        return $this->get_search_form()->get_condition();
    }

    /**
     * Gets the condition to select only learning objects in the given category
     * of any subcategory. Note that this will also initialize the category
     * menu to one with the "Search Results" item, if this has not happened
     * already.
     * @param int $category_id The category
     * @return Condition
     */
    function get_category_condition($category_id)
    {
        $subcat = array();
        $this->get_category_id_list($category_id, $this->get_category_menu(true)->_menu, $subcat);
        $conditions = array();
        foreach ($subcat as $cat)
        {
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $cat);
        }
        return (count($conditions) > 1 ? new OrCondition($conditions) : $conditions[0]);
    }

    /**
     * Determine if the given category id is valid
     * @param int $id The category id to check
     * @return boolean True if the given category is valid
     */
    /*function valid_category_id($id)
	{
		if (isset ($id) && intval($id) > 0)
		{
			if($this->retrieve_content_object($id, 'category'))
			{
				return true;
			}
			return false;
		}
		return false;
	}*/

    /**
     * @todo Move this to ContentObjectCategoryMenu or something.
     */
    private function get_category_id_list($category_id, $node, $subcat)
    {
        // XXX: Make sure we don't mess up things with trash here.
        foreach ($node as $id => $subnode)
        {
            $new_id = ($id == $category_id ? null : $category_id);
            // Null means we've reached the category we want, so we add.
            if (is_null($new_id))
            {
                $subcat[] = $id;
            }
            $this->get_category_id_list($new_id, $subnode['sub'], $subcat);
        }
    }

    /**
     * Determine the current search settings
     * @return array The current search settings
     */
    private function determine_search_settings()
    {
        if (Request :: get(self :: PARAM_CATEGORY_ID))
        {
            $this->set_parameter(self :: PARAM_CATEGORY_ID, intval(Request :: get(self :: PARAM_CATEGORY_ID)));
        }
        $form = $this->get_search_form();
        $this->search_parameters = $form->get_frozen_values();
    }

    /**
     * Gets the category menu.
     *
     * This menu contains all categories in the
     * repository of the current user. Additionally some menu items are added
     * - Recycle Bin
     * - Create a new learning object
     * - Quota
     * - Search Results (ony if search is performed)
     * @param boolean $force_search Whether the user is searching. If true,
     * overrides the default, which is to request
     * this information from the search form.
     * @return ContentObjectCategoryMenu The menu
     */
    private function get_category_menu($force_search = false)
    {
        if (! isset($this->category_menu))
        {
            // We need this because the percent sign in '%s' gets escaped.
            $temp_replacement = '__CATEGORY_ID__';
            $url_format = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS, self :: PARAM_CATEGORY_ID => $temp_replacement));
            $url_format = str_replace($temp_replacement, '%s', $url_format);
            $category = $this->get_parameter(self :: PARAM_CATEGORY_ID);
            if (! isset($category))
            {
                $category = $this->get_root_category_id();
                $this->set_parameter(self :: PARAM_CATEGORY_ID, $category);
            }
            $extra_items = array();
            $create = array();
            $create['title'] = Translation :: get('Create');
            $create['url'] = $this->get_content_object_creation_url();
            $create['class'] = 'create';

            $templates = array();
            $templates['title'] = Translation :: get('BrowseTemplates');
            $templates['url'] = $this->get_url(array(self :: PARAM_CATEGORY_ID => null, self :: PARAM_ACTION => self :: ACTION_BROWSE_TEMPLATES));
            $templates['class'] = 'template';

            $import = array();
            $import['title'] = Translation :: get('Import');
            $import['url'] = $this->get_content_object_importing_url();
            $import['class'] = 'import';

            $quota = array();
            $quota['title'] = Translation :: get('Quota');
            $quota['url'] = $this->get_quota_url();
            $quota['class'] = 'quota';

            $pub = array();
            $pub['title'] = Translation :: get('MyPublications');
            $pub['url'] = $this->get_publication_url();
            $pub['class'] = 'publication';

            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';

            $trash = array();
            $trash['title'] = Translation :: get('RecycleBin');
            $trash['url'] = $this->get_recycle_bin_url();
            if ($this->current_user_has_recycled_objects())
            {
                $trash['class'] = 'trash_full';
            }
            else
            {
                $trash['class'] = 'trash';
            }

            $uv = array();
            $uv['title'] = Translation :: get('UserViews');
            $uv['url'] = $this->get_browse_user_views_url();
            $uv['class'] = 'userview';

            $shared = array();
            $shared['title'] = Translation :: get('SharedContentObjects');
            $shared['url'] = $this->get_shared_content_objects_url();
            $shared['class'] = 'category';
            
            $shared_own = array();
            $shared_own['title'] = Translation :: get('ContentObjectsSharedByMe');
            $shared_own['url'] = $this->get_shared_content_objects_url(true);
            $shared_own['class'] = 'category';
            $shared['sub'][] = $shared_own;
            
            $shared_others = array();
            $shared_others['title'] = Translation :: get('ContentObjectsSharedWithMe');
            $shared_others['url'] = $this->get_shared_content_objects_url();
            $shared_others['class'] = 'category';
            $shared['sub'][] = $shared_others;

            $doubles = array();
            $doubles['title'] = Translation :: get('ViewDoubles');
            $doubles['url'] = $this->get_view_doubles_url();
            $doubles['class'] = 'doubles';

            $external_repository_manager_types = $this->retrieve_active_external_repository_types();

            foreach ($external_repository_manager_types as $key => $external_repository_manager_type)
            {
                $external_repository_manager_types[Translation :: get(Utilities :: underscores_to_camelcase($external_repository_manager_type))] = $external_repository_manager_type;
                unset($external_repository_manager_types[$key]);
            }

            if ($this->get_user()->is_platform_admin())
            {
                $external_repository_item = array();
                $external_repository_item['title'] = (count($external_repository_manager_types) > 0) ? Translation :: get('ExternalRepositories') : Translation :: get('ExternalRepository');
                $external_repository_item['url'] = $this->get_external_repository_instance_manager_url();
                $external_repository_item['class'] = 'external_repository';
            }

            if (count($external_repository_manager_types) > 0)
            {
                if (! $this->get_user()->is_platform_admin())
                {
                    $external_repository_item = array();
                    $external_repository_item['title'] = (count($external_repository_manager_types) > 0) ? Translation :: get('ExternalRepositories') : Translation :: get('ExternalRepository');
                    $external_repository_item['url'] = '#';
                    $external_repository_item['class'] = 'external_repository';
                }
                $external_repository_sub_items = array();

                foreach ($external_repository_manager_types as $external_repository_manager_type)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(ExternalRepository :: PROPERTY_TYPE, $external_repository_manager_type);
                    $conditions[] = new EqualityCondition(ExternalRepository :: PROPERTY_ENABLED, 1);
                    $condition = new AndCondition($conditions);
                    $external_repository_managers = $this->retrieve_external_repositories($condition, 0, - 1, new ObjectTableOrder(ExternalRepository :: PROPERTY_TITLE));

                    if ($external_repository_managers->size() > 1)
                    {
                        $external_repository_type_item = array();
                        $external_repository_type_item['title'] = Utilities :: underscores_to_camelcase($external_repository_manager_type);
                        $external_repository_type_item['url'] = '#';
                        $external_repository_type_item['class'] = $external_repository_manager_type;
                        $external_repository_type_subitems = array();

                        while ($external_repository_manager = $external_repository_managers->next_result())
                        {
                            if (! RepositoryRights :: is_allowed_in_external_repositories_subtree(RepositoryRights :: USE_RIGHT, $external_repository_manager->get_id()))
                            {
                                continue;
                            }

                            $external_repository_type_subitem = array();
                            $external_repository_type_subitem['title'] = $external_repository_manager->get_title();
                            $external_repository_type_subitem['url'] = $this->get_url(array(
                                    Application :: PARAM_ACTION => self :: ACTION_EXTERNAL_REPOSITORY_MANAGER,
                                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY => $external_repository_manager->get_id()), array(
                                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: PARAM_RENDERER));
                            $external_repository_type_subitem['class'] = $external_repository_manager->get_type();
                            $external_repository_type_subitems[] = $external_repository_type_subitem;
                        }
                        $external_repository_type_item['sub'] = $external_repository_type_subitems;
                        $external_repository_sub_items[] = $external_repository_type_item;
                    }
                    else
                    {
                        $external_repository_manager = $external_repository_managers->next_result();

                        if (RepositoryRights :: is_allowed_in_external_repositories_subtree(RepositoryRights :: USE_RIGHT, $external_repository_manager->get_id()))
                        {

                            $external_repository_sub_item = array();
                            $external_repository_sub_item['title'] = $external_repository_manager->get_title();
                            $external_repository_sub_item['url'] = $this->get_url(array(
                                    Application :: PARAM_ACTION => self :: ACTION_EXTERNAL_REPOSITORY_MANAGER,
                                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY => $external_repository_manager->get_id()), array(
                                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: PARAM_RENDERER));
                            $external_repository_sub_item['class'] = $external_repository_manager->get_type();
                            $external_repository_sub_items[] = $external_repository_sub_item;
                        }
                    }
                }

                $external_repository_item['sub'] = $external_repository_sub_items;
            }

            $content_object_managers = RepositoryDataManager :: get_content_object_managers();

            if (count($content_object_managers) > 0)
            {
                $content_object_manage_item = array();
                $content_object_manage_item['title'] = Translation :: get('ManageContentObjects');
                $content_object_manage_item['url'] = '#';
                $content_object_manage_item['class'] = 'manage';

                $content_object_manage_items = array();

                foreach ($content_object_managers as $content_object_type => $managers)
                {
                    foreach ($managers as $manager)
                    {
                        $content_object_manage_sub_item = array();
                        $content_object_manage_sub_item['title'] = Translation :: get(Utilities :: underscores_to_camelcase($content_object_type . '_' . $manager) . 'Manager');
                        $content_object_manage_sub_item['url'] = $this->get_content_object_manager_url($content_object_type, $manager);
                        $content_object_manage_sub_item['class'] = 'type_' . $content_object_type;
                        $content_object_manage_sub_items[] = $content_object_manage_sub_item;
                    }
                }

                $content_object_manage_item['sub'] = $content_object_manage_sub_items;
            }

            $extra_items[] = $shared;
            $extra_items[] = $pub;

            //            if (isset($external_repository) && count($external_repository['sub']) > 0)
            //            {
            //                $extra_items[] = $external_repository;
            //            }


            if (isset($external_repository_item) && count($external_repository_item['sub']) > 0)
            {
                $extra_items[] = $external_repository_item;
            }

            if (isset($streaming_item) && count($streaming_item['sub']) > 0)
            {
                $extra_items[] = $streaming_item;
            }

            $extra_items[] = $line;

            $extra_items[] = $create;
            $extra_items[] = $import;
            $extra_items[] = $templates;

            if (isset($content_object_manage_item))
            {
                $extra_items[] = $content_object_manage_item;
            }

            $extra_items[] = $line;

            $extra_items[] = $quota;
            $extra_items[] = $uv;
            $extra_items[] = $doubles;
            $extra_items[] = $trash;

            if ($force_search || $this->get_search_form()->validate())
            {
                $search_url = '#';
                $search = array();
                $search['title'] = Translation :: get('SearchResults');
                $search['url'] = $search_url;
                $search['class'] = 'search_results';
                $extra_items[] = $search;
            }
            else
            {
                $search_url = null;
            }
            $this->category_menu = new ContentObjectCategoryMenu($this->get_user_id(), $category, $url_format, $extra_items);
            if (isset($search_url))
            {
                $this->category_menu->forceCurrentUrl($search_url, true);
            }
        }
        return $this->category_menu;
    }

    /**
     * Return a condition object that can be used to look for objects of the current logged user that are recycled
     *
     * @return AndCondition
     */
    public function get_current_user_recycle_bin_conditions()
    {
        return new AndCondition(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id()), new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_RECYCLED));
    }

    /**
     *
     * @return boolean
     */
    public function current_user_has_recycled_objects()
    {
        return $this->count_content_objects($this->get_current_user_recycle_bin_conditions()) > 0;
    }

    /**
     * Gets the search form.
     * @return RepositorySearchForm The search form.
     */
    private function get_search_form()
    {
        if (! isset($this->search_form))
        {
            $this->search_form = new RepositorySearchForm($this, $this->get_url());
        }
        return $this->search_form;
    }

    /**
     * Displays the tree menu.
     */
    private function display_content_object_categories()
    {
        echo $this->get_category_menu()->render_as_tree();
    }

    /**
     * Displays the search form.
     */
    private function display_search_form()
    {
        echo $this->get_search_form()->display();
    }

    public static function get_application_platform_admin_links()
    {
        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);

        $links[] = new DynamicAction(Translation :: get('ImportTemplate'), Translation :: get('ImportTemplateDescription'), Theme :: get_image_path() . 'browse_import.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_IMPORT_TEMPLATE), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('ManageExternalRepositoryManagerInstances'), Translation :: get('ManageExternalRepositoryManagerInstancesDescription'), Theme :: get_image_path() . 'browse_repository.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_EXTERNAL_REPOSITORY_INSTANCES), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('ManageContentObjectTypes'), Translation :: get('ManageContentObjectTypesDescription'), Theme :: get_image_path() . 'browse_repository.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_CONTENT_OBJECT_REGISTRATIONS), array(), false, Redirect :: TYPE_CORE));

        $info['search'] = Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS), array(), false, Redirect :: TYPE_CORE);
        $info['links'] = $links;
        return $info;
    }

    /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public function get_application_platform_import_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('ImportTemplates'), 'description' => Translation :: get('ImportTemplatesDescription'), 'url' => $this->get_link(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_TEMPLATE)));

        return $links;
    }

    static function get_document_downloader_url($document_id)
    {
        $parameters = array(self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENT, self :: PARAM_CONTENT_OBJECT_ID => $document_id);
        return Redirect :: get_link(self :: APPLICATION_NAME, $parameters, null, null, Redirect :: TYPE_CORE);
    }

    function count_complex_content_object_items($condition)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->count_complex_content_object_items($condition);
    }

    function retrieve_complex_content_object_items($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_complex_content_object_items($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_complex_content_object_item($complex_content_object_item_id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
    }

    function get_complex_content_object_item_edit_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEMS, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_complex_content_object_item_delete_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEMS, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_complex_content_object_item_move_url($complex_content_object_item, $root_id, $direction)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEMS, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id, self :: PARAM_MOVE_DIRECTION => $direction, 'publish' => Request :: get('publish')));
    }

    function get_browse_complex_content_object_url($object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT, self :: PARAM_CONTENT_OBJECT_ID => $object->get_id()));
    }

    function get_add_existing_content_object_url($root_id, $complex_content_object_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_SELECT_CONTENT_OBJECTS, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id,
                'publish' => Request :: get('publish')));
    }

    function get_add_content_object_url($content_object, $complex_content_object_item_id, $root_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_ADD_CONTENT_OBJECT, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_REF => $content_object->get_id(), self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_content_object_exporting_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_CONTENT_OBJECTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    function get_publish_content_object_url($content_object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_CONTENT_OBJECT, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    function count_categories($conditions = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->count_categories($conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function count_user_views($conditions = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->count_user_views($conditions);
    }

    function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_user_views($condition, $offset, $count, $order_property);
    }

    function retrieve_content_object_metadata($condition = null, $offset = null, $max_objects = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object_metadata($condition, $offset, $max_objects, $order_property);
    }

    function retrieve_content_object_metadata_catalog($condition = null, $offset = null, $max_objects = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object_metadata_catalog($condition, $offset, $max_objects, $order_property);
    }

    function retrieve_external_repository_condition($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RepositoryDataManager :: get_instance()->retrieve_external_repository_condition($condition, $offset, $count, $order_property);
    }

    function retrieve_external_repository($external_repository_id)
    {
        return RepositoryDataManager :: get_instance()->retrieve_external_repository($external_repository_id);
    }

    function retrieve_active_external_repository_types()
    {
        return RepositoryDataManager :: get_instance()->retrieve_active_external_repository_types();
    }

    function retrieve_external_repositories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RepositoryDataManager :: get_instance()->retrieve_external_repositories($condition, $offset, $count, $order_property);
    }

    function count_external_repositories($condition = null)
    {
        return RepositoryDataManager :: get_instance()->count_external_repositories($condition);
    }

    /**
     * Renders the users block and returns it.
     */
    function render_block($block)
    {
        $repository_block = RepositoryBlock :: factory($this, $block);
        return $repository_block->run();
    }

    function get_browse_user_views_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_USER_VIEWS));
    }

    function get_shared_content_objects_url($show_objects_shared_by_me = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS, self :: PARAM_CATEGORY_ID => null, self :: PARAM_SHOW_OBJECTS_SHARED_BY_ME => $show_objects_shared_by_me));
    }

    function create_user_view_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_USER_VIEW));
    }

    function update_user_view_url($user_view_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_USER_VIEW, self :: PARAM_USER_VIEW => $user_view_id));
    }

    function delete_user_view_url($user_view_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_USER_VIEW, self :: PARAM_USER_VIEW => $user_view_id));
    }

    function get_copy_content_object_url($content_object_id, $to_user_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COPY_CONTENT_OBJECT, self :: PARAM_CONTENT_OBJECT_ID => $content_object_id, self :: PARAM_TARGET_USER => $to_user_id));
    }

    function get_import_template_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_TEMPLATE));
    }

    function get_delete_template_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_TEMPLATE, self :: PARAM_CONTENT_OBJECT_ID => $template_id));
    }

    function get_view_doubles_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_DOUBLES));
    }

    function get_delete_link_url($type, $object_id, $link_id)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_LINK;
        $parameters[self :: PARAM_LINK_TYPE] = $type;
        $parameters[self :: PARAM_CONTENT_OBJECT_ID] = $object_id;
        $parameters[self :: PARAM_LINK_ID] = $link_id;

        return $this->get_url($parameters);
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function has_right($content_object, $user_id, $right)
    {
        $udm = UserDataManager :: get_instance();
        $rdm = RightsDataManager :: get_instance();

        $content_object = $this->retrieve_content_object($content_object->get_id());
        if ($content_object->get_owner_id() == 0)
            return true;

        $user = $udm->retrieve_user($user_id);
        $groups = $user->get_groups();
        foreach ($groups as $group)
        {
            $group_ids[] = $group->get_id();
        }

        $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
        $rights_db = $reflect->getConstants();

        foreach ($rights_db as $right_id)
        {
            if ($right_id != RepositoryRights :: VIEW_RIGHT && $right_id != RepositoryRights :: USE_RIGHT && $right_id != RepositoryRights :: REUSE_RIGHT)
                continue;
            $rights[] = $right_id;
        }

        $location_ids = array();
        $shared_content_objects = $rdm->retrieve_shared_content_objects_for_user($user->get_id(), $rights);

        while ($user_right_location = $shared_content_objects->next_result())
        {
            if (! in_array($user_right_location->get_location_id(), $location_ids))
                $location_ids[] = $user_right_location->get_location_id();

            $list[] = array('location_id' => $user_right_location->get_location_id(), 'user' => $user_right_location->get_user_id(), 'right' => $user_right_location->get_right_id());
        }

        $shared_content_objects = $rdm->retrieve_shared_content_objects_for_groups($group_ids, $rights);

        while ($group_right_location = $shared_content_objects->next_result())
        {
            if (! in_array($group_right_location->get_location_id(), $location_ids))
                $location_ids[] = $group_right_location->get_location_id();

            $list[] = array('location_id' => $group_right_location->get_location_id(), 'group' => $group_right_location->get_group_id(), 'right' => $group_right_location->get_right_id());
        }

        if (count($location_ids) > 0)
        {
            $location_cond = new InCondition('id', $location_ids);
            $locations = $rdm->retrieve_locations($location_cond);

            while ($location = $locations->next_result())
            {
                foreach ($list as $key => $value)
                {
                    if ($value['location_id'] == $location->get_id())
                    {
                        $value['content_object'] = $location->get_identifier();
                        $list[$key] = $value;
                    }
                }
            }
        }

        foreach ($list as $key => $value)
        {
            if ($value['content_object'] == $content_object->get_id() && $value['right'] == $right)
                return true;
        }
        return false;
    } //has_right


    function get_create_user_view_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_USER_VIEW));
    }

    function get_update_user_view_url($user_view_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_USER_VIEW, self :: PARAM_USER_VIEW => $user_view_id));
    }

    function get_delete_user_view_url($user_view_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_USER_VIEW, self :: PARAM_USER_VIEW => $user_view_id));
    }
    
	function get_content_object_share_browser_url($content_object_ids)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTENT_OBJECT_SHARE_BROWSER, self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids));
    }

    function get_content_object_share_create_url($content_object_ids)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTENT_OBJECT_SHARE_CREATOR, self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids));
    }

    function get_content_object_share_deleter_url($content_object_ids, $user_ids = null, $group_ids = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTENT_OBJECT_SHARE_DELETER, self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids,
        						   self :: PARAM_TARGET_USER => $user_ids, self :: PARAM_TARGET_GROUP => $group_ids));
    }
    
	function get_content_object_share_editor_url($content_object_ids, $user_ids = null, $group_ids = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTENT_OBJECT_SHARE_EDITOR, self :: PARAM_CONTENT_OBJECT_ID => $content_object_ids,
        						   self :: PARAM_TARGET_USER => $user_ids, self :: PARAM_TARGET_GROUP => $group_ids));
    }

    function get_renderer()
    {
        $renderer = Request :: get(self :: PARAM_RENDERER);

        if ($renderer && in_array($renderer, $this->get_available_renderers()))
        {
            return $renderer;
        }
        else
        {
            $renderers = $this->get_available_renderers();
            return $renderers[0];
        }
    }

    function get_available_renderers()
    {
        return array(ContentObjectRenderer :: TYPE_TABLE, ContentObjectRenderer :: TYPE_GALLERY, ContentObjectRenderer :: TYPE_SLIDESHOW);
    }

    function get_external_repository_instance_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_EXTERNAL_REPOSITORY_INSTANCES));
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>
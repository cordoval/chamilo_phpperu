<?php
/**
 * $Id: repository_manager.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager
 */


/**
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
    const PARAM_CONTENT_OBJECT_TYPE = 'type';
    const PARAM_DELETE_PERMANENTLY = 'delete_permanently';
    const PARAM_DELETE_VERSION = 'delete_version';
    const PARAM_DELETE_RECYCLED = 'delete_recycle';
    const PARAM_EXPORT_SELECTED = 'export_selected';
    const PARAM_EMPTY_RECYCLE_BIN = 'empty';
    const PARAM_RECYCLE_SELECTED = 'recycle_selected';
    const PARAM_MOVE_SELECTED = 'move_selected';
    const PARAM_RESTORE_SELECTED = 'restore_selected';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_PUBLISH_SELECTED = 'publish_selected';
    const PARAM_COMPARE_OBJECT = 'object';
    const PARAM_COMPARE_VERSION = 'compare';
    const PARAM_PUBLICATION_APPLICATION = 'application';
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
    const PARAM_TARGET_USER = 'target';
    const PARAM_DELETE_TEMPLATES = 'delete_templates';
    const PARAM_COPY_FROM_TEMPLATES = 'copy_template';
    const PARAM_COPY_TO_TEMPLATES = 'copy_to_template';
    const PARAM_EXTERNAL_OBJECT_ID = 'external_object_id';
    const PARAM_EXTERNAL_REPOSITORY_ID = 'ext_rep_id';
    
    /**#@-*/
    /**#@+
     * Constant defining an action of the repository manager.
     */
    const ACTION_BROWSE_CONTENT_OBJECTS = 'browse';
    const ACTION_BROWSE_SHARED_CONTENT_OBJECTS = 'browse_shared';
    const ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS = 'recycler';
    const ACTION_VIEW_CONTENT_OBJECTS = 'view';
    const ACTION_CREATE_CONTENT_OBJECTS = 'create';
    const ACTION_EDIT_CONTENT_OBJECTS = 'edit';
    const ACTION_REVERT_CONTENT_OBJECTS = 'revert';
    const ACTION_DELETE_CONTENT_OBJECTS = 'delete';
    const ACTION_DELETE_CONTENT_OBJECT_PUBLICATIONS = 'deletepublications';
    const ACTION_RESTORE_CONTENT_OBJECTS = 'restore';
    const ACTION_MOVE_CONTENT_OBJECTS = 'move';
    const ACTION_EDIT_CONTENT_OBJECT_METADATA = 'metadata_edit';
    const ACTION_VIEW_CONTENT_OBJECT_METADATA = 'metadata_view';
    const ACTION_EDIT_CONTENT_OBJECT_RIGHTS = 'rights';
    const ACTION_VIEW_MY_PUBLICATIONS = 'publicationbrowser';
    const ACTION_VIEW_QUOTA = 'quota';
    const ACTION_COMPARE_CONTENT_OBJECTS = 'compare';
    const ACTION_UPDATE_CONTENT_OBJECT_PUBLICATION = 'publicationupdater';
    const ACTION_CREATE_COMPLEX_CONTENT_OBJECTS = 'createcomplex';
    const ACTION_UPDATE_COMPLEX_CONTENT_OBJECTS = 'updatecomplex';
    const ACTION_DELETE_COMPLEX_CONTENT_OBJECTS = 'deletecomplex';
    const ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS = 'browsecomplex';
    const ACTION_MOVE_COMPLEX_CONTENT_OBJECTS = 'movecomplex';
    const ACTION_SELECT_CONTENT_OBJECTS = 'selectobjects';
    const ACTION_ADD_CONTENT_OBJECT = 'addobject';
    const ACTION_EXPORT_CONTENT_OBJECTS = 'export';
    const ACTION_IMPORT_CONTENT_OBJECTS = 'import';
    const ACTION_PUBLISH_CONTENT_OBJECT = 'publish';
    const ACTION_MANAGE_CATEGORIES = 'manage_categories';
    const ACTION_VIEW_ATTACHMENT = 'view_attachment';
    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'build_complex';
    const ACTION_VIEW_REPO = 'repo_viewer';
    const ACTION_DOWNLOAD_DOCUMENT = 'document_downloader';
    const ACTION_EXTERNAL_REPOSITORY_BROWSE = 'ext_rep_browse';
    const ACTION_EXTERNAL_REPOSITORY_EXPORT = 'ext_rep_export';
    const ACTION_EXTERNAL_REPOSITORY_IMPORT = 'ext_rep_import';
    const ACTION_EXTERNAL_REPOSITORY_LIST_OBJECTS = 'ext_rep_list_objects';
    const ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW = 'ext_rep_metadata_review';
    const ACTION_EXTERNAL_REPOSITORY_CATALOG = 'ext_rep_catalog';
    const ACTION_BROWSE_TEMPLATES = 'templates';
    const ACTION_COPY_CONTENT_OBJECT = 'lo_copy';
    const ACTION_IMPORT_TEMPLATE = 'import_template';
    const ACTION_DELETE_TEMPLATE = 'delete_template';

    const ACTION_BROWSE_USER_VIEWS = 'browse_views';
    const ACTION_CREATE_USER_VIEW = 'create_view';
    const ACTION_DELETE_USER_VIEW = 'delete_view';
    const ACTION_UPDATE_USER_VIEW = 'update_view';

    const PARAM_USER_VIEW = 'user_view';

    /**
     * Property of this repository manager.
     */
    private $search_parameters;
    private $search_form;
    private $category_menu;
    private $quota_url;
    private $publication_url;
    private $create_url;
    private $import_url;
    private $recycle_bin_url;

    /**
     * Constructor
     * @param int $user_id The user id of current user
     */
    function RepositoryManager($user)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
        $this->determine_search_settings();
    }

    /**
     * Run this repository manager
     */
    function run()
    {
        $this->publication_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MY_PUBLICATIONS), false, false, 'dddd');
        $this->quota_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_QUOTA, self :: PARAM_CATEGORY_ID => null));
        $this->create_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECTS));
        $this->import_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_CONTENT_OBJECTS));
        $this->recycle_bin_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS, self :: PARAM_CATEGORY_ID => null));

        /*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
        //$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ComplexCreator', $this);
                break;
            case self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ComplexUpdater', $this);
                break;
            case self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ComplexDeleter', $this);
                break;
            case self :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ComplexBrowser', $this);
                break;
            case self :: ACTION_SELECT_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ContentObjectSelector', $this);
                break;
            case self :: ACTION_ADD_CONTENT_OBJECT :
                $component = RepositoryManagerComponent :: factory('AddContentObjects', $this);
                break;
            case self :: ACTION_VIEW_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_COMPARE_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Comparer', $this);
                break;
            case self :: ACTION_CREATE_CONTENT_OBJECTS :
                $this->force_menu_url($this->create_url, true);
                $component = RepositoryManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_EDIT_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_REVERT_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Reverter', $this);
                break;
            case self :: ACTION_DELETE_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_DELETE_CONTENT_OBJECT_PUBLICATIONS :
                $component = RepositoryManagerComponent :: factory('PublicationDeleter', $this);
                break;
            case self :: ACTION_RESTORE_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Restorer', $this);
                break;
            case self :: ACTION_MOVE_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Mover', $this);
                break;
            case self :: ACTION_EDIT_CONTENT_OBJECT_METADATA :
                $component = RepositoryManagerComponent :: factory('MetadataEditor', $this);
                break;
            case self :: ACTION_VIEW_CONTENT_OBJECT_METADATA :
                $component = RepositoryManagerComponent :: factory('MetadataViewer', $this);
                break;
            case self :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS :
                $component = RepositoryManagerComponent :: factory('RightsEditor', $this);
                break;
            case self :: ACTION_UPDATE_CONTENT_OBJECT_PUBLICATION :
                $component = RepositoryManagerComponent :: factory('PublicationUpdater', $this);
                break;
            case self :: ACTION_VIEW_QUOTA :
                $this->set_parameter(self :: PARAM_CATEGORY_ID, null);
                $this->force_menu_url($this->quota_url, true);
                $component = RepositoryManagerComponent :: factory('QuotaViewer', $this);
                break;
            case self :: ACTION_VIEW_MY_PUBLICATIONS :
                $this->set_parameter(self :: PARAM_CATEGORY_ID, null);
                $this->force_menu_url($this->publication_url, true);
                $component = RepositoryManagerComponent :: factory('PublicationBrowser', $this);
                break;
            case self :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS :
                $this->set_parameter(self :: PARAM_CATEGORY_ID, null);
                $this->force_menu_url($this->recycle_bin_url, true);
                $component = RepositoryManagerComponent :: factory('RecycleBinBrowser', $this);
                break;
            case self :: ACTION_MOVE_COMPLEX_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ComplexOrderMover', $this);
                break;
            case self :: ACTION_EXPORT_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('Exporter', $this);
                break;
            case self :: ACTION_IMPORT_CONTENT_OBJECTS :
                $this->force_menu_url($this->import_url, true);
                $component = RepositoryManagerComponent :: factory('Importer', $this);
                break;
            case self :: ACTION_PUBLISH_CONTENT_OBJECT :
                $component = RepositoryManagerComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = RepositoryManagerComponent :: factory('CategoryManager', $this);
                break;
            case self :: ACTION_DOWNLOAD_DOCUMENT :
                $component = RepositoryManagerComponent :: factory('DocumentDownloader', $this);
                break;
            case self :: ACTION_BROWSE_USER_VIEWS :
                $component = RepositoryManagerComponent :: factory('UserViewBrowser', $this);
                break;
            case self :: ACTION_CREATE_USER_VIEW :
                $component = RepositoryManagerComponent :: factory('UserViewCreator', $this);
                break;
            case self :: ACTION_UPDATE_USER_VIEW :
                $component = RepositoryManagerComponent :: factory('UserViewUpdater', $this);
                break;
            case self :: ACTION_DELETE_USER_VIEW :
                $component = RepositoryManagerComponent :: factory('UserViewDeleter', $this);
                break;
            case self :: ACTION_VIEW_ATTACHMENT :
                $component = RepositoryManagerComponent :: factory('AttachmentViewer', $this);
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT :
                $component = RepositoryManagerComponent :: factory('ComplexBuilder', $this);
                break;
            case self :: ACTION_VIEW_REPO :
                $component = RepositoryManagerComponent :: factory('RepoViewer', $this);
                break;
            case self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS :
                $component = RepositoryManagerComponent :: factory('SharedContentObjectsBrowser', $this);
                break;
            case self :: ACTION_EXTERNAL_REPOSITORY_BROWSE :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryExportBrowser', $this);
                break;
            case self :: ACTION_EXTERNAL_REPOSITORY_EXPORT :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryExportExport', $this);
                break;
            case self :: ACTION_EXTERNAL_REPOSITORY_IMPORT :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryExportImport', $this);
                break;    
            case self :: ACTION_EXTERNAL_REPOSITORY_LIST_OBJECTS :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryExportListObjects', $this);
                break;
            case self :: ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryMetadataReviewer', $this);
                break;
            case self :: ACTION_EXTERNAL_REPOSITORY_CATALOG :
                $component = RepositoryManagerComponent :: factory('ExternalRepositoryExportCatalog', $this);
                break;
            case self :: ACTION_BROWSE_TEMPLATES :
                $component = RepositoryManagerComponent :: factory('TemplateBrowser', $this);
                break;
            case self :: ACTION_COPY_CONTENT_OBJECT :
                $component = RepositoryManagerComponent :: factory('ContentObjectCopier', $this);
                break;
            case self :: ACTION_IMPORT_TEMPLATE :
                $component = RepositoryManagerComponent :: factory('TemplateImporter', $this);
                break;
            case self :: ACTION_DELETE_TEMPLATE :
                $component = RepositoryManagerComponent :: factory('TemplateDeleter', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_CONTENT_OBJECTS);
                $component = RepositoryManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    /**
     * @todo Clean this up. It's all SortableTable's fault. :-(
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[RepositoryBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }

            $template_ids = $_POST[TemplateBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($template_ids))
            {
                $template_ids = array();
            }
            elseif (! is_array($template_ids))
            {
                $template_ids = array($template_ids);
            }

            switch ($_POST['action'])
            {
                case self :: PARAM_RECYCLE_SELECTED :
                    $this->set_action(self :: ACTION_DELETE_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_DELETE_RECYCLED, 1);
                    break;
                case self :: PARAM_MOVE_SELECTED :
                    $this->set_action(self :: ACTION_MOVE_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    break;
                case self :: PARAM_RESTORE_SELECTED :
                    $this->set_action(self :: ACTION_RESTORE_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    break;
                case self :: PARAM_DELETE_SELECTED :
                    $this->set_action(self :: ACTION_DELETE_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_DELETE_PERMANENTLY, 1);
                    break;
                case self :: PARAM_REMOVE_SELECTED_CLOI :
                    $this->set_action(self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CLOI_ID, $selected_ids);
                    break;
                case self :: PARAM_ADD_OBJECTS :
                    $this->set_action(self :: ACTION_ADD_CONTENT_OBJECT);
                    Request :: set_get(self :: PARAM_CLOI_REF, $selected_ids);
                    break;
                case self :: PARAM_PUBLISH_SELECTED :
                    $this->set_action(self :: ACTION_PUBLISH_CONTENT_OBJECT);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    break;
                case self :: PARAM_DELETE_SELECTED_USER_VIEW :
                    $this->set_action(self :: ACTION_DELETE_USER_VIEW);
                    Request :: set_get(self :: PARAM_USER_VIEW, $selected_ids);
                    break;
                case self :: PARAM_COPY_TO_TEMPLATES :
                    $this->set_action(self :: ACTION_COPY_CONTENT_OBJECT);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_TARGET_USER, 0);
                    break;
                case self :: PARAM_COPY_FROM_TEMPLATES :
                    $this->set_action(self :: ACTION_COPY_CONTENT_OBJECT);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $template_ids);
                    Request :: set_get(self :: PARAM_TARGET_USER, $this->get_user_id());
                    break;
                case self :: PARAM_DELETE_TEMPLATES :
                    $this->set_action(self :: ACTION_DELETE_TEMPLATE);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $template_ids);
                    break;
                case self :: PARAM_EXPORT_SELECTED :
                    $this->set_action(self :: ACTION_EXPORT_CONTENT_OBJECTS);
                    Request :: set_get(self :: PARAM_CONTENT_OBJECT_ID, $selected_ids);
                    break;
            }
        }
    }

    /**
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail, $display_search = false, $display_menu = true)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = new BreadcrumbTrail();
        }

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));

        /*$categories = $this->breadcrumbs;
		if (count($categories) > 0 && $this->get_action() == self :: ACTION_BROWSE_CONTENT_OBJECTS)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}*/

        if ($display_menu)
        {
            if (Request :: get('category'))
                $trail->merge($this->get_category_menu()->get_breadcrumbs(false));
        }

        $trail->merge($breadcrumbtrail);

        $breadcrumbtrail = $trail;

        $title = $breadcrumbtrail->get_last()->get_name();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        Display :: header($breadcrumbtrail);

        if ($display_menu)
        {
            echo '<div id="repository_tree_container" style="float: left; width: 12%;">';
            $this->display_content_object_categories();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
        }
        else
        {
            echo '<div>';
        }

        echo '<div>';
        echo '<h3 style="float: left;" title="' . $title . '">' . $title_short . '</h3>';
        if ($display_search)
        {
            $this->display_search_form();
        }
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        if ($msg = Request :: get(Application :: PARAM_MESSAGE))
        {
            $this->display_message($msg);
        }
        if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
        {
            $this->display_error_message($msg);
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
        return $this->quota_url;
    }

    /**
     * Gets the URL to the publication page.
     * @return string The URL.
     */
    function get_publication_url()
    {
        return $this->publication_url;
    }

    /**
     * Gets the URL to the learning object creation page.
     * @return string The URL.
     */
    function get_content_object_creation_url()
    {
        return $this->create_url;
    }

    /**
     * Gets the URL to the learning object import page.
     * @return string The URL.
     */
    function get_content_object_importing_url()
    {
        return $this->import_url;
    }

    /**
     * Gets the URL to the recycle bin.
     * @return string The URL.
     */
    function get_recycle_bin_url()
    {
        return $this->recycle_bin_url;
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
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->count_publication_attributes($user, $type, $condition);
    }

    /**
     * @see RepositoryDataManager::content_object_deletion_allowed()
     */
    function content_object_deletion_allowed($content_object, $type = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->content_object_deletion_allowed($content_object, $type);
    }

    /**
     * @see RepositoryDataManager::content_object_revert_allowed()
     */
    function content_object_revert_allowed($content_object)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->content_object_revert_allowed($content_object);
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attributes()
     */
    function get_registered_types($only_master_types = false)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->get_registered_types($only_master_types);
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attributes()
     */
    function get_content_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->get_content_object_publication_attributes($user, $id, $type, $offset, $count, $order_property);
    }

    /**
     * @see RepositoryDataManager::get_content_object_publication_attribute()
     */
    function get_content_object_publication_attribute($id, $application)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->get_content_object_publication_attribute($id, $application);
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECT_PUBLICATIONS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    /**
     * Gets the defined learning object types
     * @see RepositoryDataManager::get_registered_types()
     * @param boolean $only_master_types Only return the master type learning
     * objects (which can exist on their own). Returns all learning object types
     * by default.
     */
    function get_content_object_types($only_master_types = false)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->get_registered_types($only_master_types);
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
     *                              overrides the default, which is to request
     *                              this information from the search form.
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
            if ($this->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id()), ContentObject :: STATE_RECYCLED))
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

            $extra_items[] = $shared;
            $extra_items[] = $pub;

            $extra_items[] = $line;

            $extra_items[] = $create;
            $extra_items[] = $import;
            $extra_items[] = $templates;

            $extra_items[] = $line;

            $extra_items[] = $quota;
            $extra_items[] = $uv;
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

    public function get_application_platform_admin_links()
    {
        $info = parent :: get_application_platform_admin_links();

        $links[] = array('name' => Translation :: get('ImportTemplate'), 'description' => Translation :: get('ImportTemplateDescription'), 'action' => 'import', 'url' => $this->get_link(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_TEMPLATE)));

        $info['search'] = $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS));
        $info['links'] = $links;
        return $info;
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

    function retrieve_complex_content_object_item($cloi_id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_complex_content_object_item($cloi_id);
    }

    function get_complex_content_object_item_edit_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECTS, self :: PARAM_CLOI_ID => $cloi->get_id(), self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_complex_content_object_item_delete_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECTS, self :: PARAM_CLOI_ID => $cloi->get_id(), self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_complex_content_object_item_move_url($cloi, $root_id, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_COMPLEX_CONTENT_OBJECTS, self :: PARAM_CLOI_ID => $cloi->get_id(), self :: PARAM_CLOI_ROOT_ID => $root_id, self :: PARAM_MOVE_DIRECTION => $direction, 'publish' => Request :: get('publish')));
    }

    function get_browse_complex_content_object_url($object)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT, ComplexBuilder :: PARAM_ROOT_LO => $object->get_id()));
    }

    function get_add_existing_content_object_url($root_id, $clo_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SELECT_CONTENT_OBJECTS, self :: PARAM_CLOI_ID => $clo_id, self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
    }

    function get_add_content_object_url($content_object, $cloi_id, $root_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADD_CONTENT_OBJECT, self :: PARAM_CLOI_REF => $content_object->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
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

    function retrieve_external_export($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_external_export($condition, $offset, $count, $order_property);
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

    function get_shared_content_objects_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS));
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

    function get_copy_content_object_url($lo_id, $to_user_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COPY_CONTENT_OBJECT, self :: PARAM_CONTENT_OBJECT_ID => $lo_id, self :: PARAM_TARGET_USER => $to_user_id));
    }

    function get_import_template_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_TEMPLATE));
    }

    function get_delete_template_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_TEMPLATE, self :: PARAM_CONTENT_OBJECT_ID => $template_id));
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

        $lo = $this->retrieve_content_object($content_object->get_id());
        if ($lo->get_owner_id() == 0)
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
}
?>
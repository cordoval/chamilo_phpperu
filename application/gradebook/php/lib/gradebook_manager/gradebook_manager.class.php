<?php
require_once Path :: get_library_path() . 'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_application_path() . 'lib/gradebook/gradebook_rights.class.php';

require_once dirname(__FILE__) . '/../gradebook_utilities.class.php';
require_once dirname(__FILE__) . '/../data_provider/gradebook_tree_menu_data_provider.class.php';
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';

require_once dirname(__FILE__) . '/component/evaluation_formats_browser/evaluation_formats_browser_table.class.php';
require_once dirname(__FILE__) . '/component/gradebook_external_publication_browser/gradebook_external_publication_browser_table.class.php';

class GradebookManager extends WebApplication
{
    const APPLICATION_NAME = 'gradebook';
    const PARAM_ACTION = 'go';

    /*
	 * Gradebook administration actions
	 */
    const ACTION_BROWSE_GRADEBOOK = 'gradebook_browser';
    const ACTION_ADMIN_BROWSE_EVALUATION_FORMATS = 'admin_evaluation_formats_browser';
    const ACTION_EDIT_EVALUATION_FORMAT = 'admin_edit_evaluation_format';
    const ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY = 'admin_active_changer';
    const ACTION_VIEW_EVALUATIONS_ON_PUBLICATION = 'view_evaluations_on_publication';
    const ACTION_EDIT_EXTERNAL_EVALUATION = 'edit_external_evaluation';
    const ACTION_DELETE_EXTERNAL_EVALUATION = 'delete_external_evaluation';
    const ACTION_CREATE_EXTERNAL_GRADE = 'external_grade_evaluation_input';
    const ACTION_CREATE_EXTERNAL = 'external_creator';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_GRADEBOOK;

    /*
	 * Gradebook parameters
	 */
    const PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT = 'activate_selected_evaluation_format';
    const PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT = 'deactivate_selected_evaluation_format';
    const PARAM_DELETE_SELECTED_EXTERNAL_EVALUATION = 'delete_selected_external_evaluation';
    const PARAM_EVALUATION_FORMAT = 'evaluation_format';
    const PARAM_EVALUATION_FORMAT_ID = 'evaluation_format';
    const PARAM_ACTIVE = 'active';
    const PARAM_PUBLICATION_TYPE = 'publication_type';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_PUBLICATION_APP = 'publication_app';

    public function GradebookManager($user)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('EvaluationFormatTypeList'), Translation :: get('EvaluationFormatTypeListDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS)));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        return $info;
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            if (isset($_POST[EvaluationFormatsBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[EvaluationFormatsBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            if (isset($_POST[GradebookExternalPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[GradebookExternalPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT :
                    $this->set_action(self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY);
                    Request :: set_get(self :: PARAM_EVALUATION_FORMAT_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_ACTIVE, 0);
                    break;
                case self :: PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT :
                    $this->set_action(self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY);
                    Request :: set_get(self :: PARAM_EVALUATION_FORMAT_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_ACTIVE, 1);
                    break;
                case self :: PARAM_DELETE_SELECTED_EXTERNAL_EVALUATION :
                    $this->set_action(self :: ACTION_DELETE_EXTERNAL_EVALUATION);
                    Request :: set_get(self :: PARAM_PUBLICATION_ID, $selected_ids);
                    break;
            }
        }
    }

    // Data retrieval
    // **************
    // evaluation formats
    function count_evaluation_formats()
    {
        return GradebookDataManager :: get_instance()->count_evaluation_formats();
    }

    function retrieve_evaluation_formats($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation_formats($condition, $offset, $count, $order_property);
    }

    function retrieve_evaluation_format($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation_format($id);
    }

    // applications
    function retrieve_internal_item_applications()
    {
        return GradebookDataManager :: get_instance()->retrieve_internal_item_applications();
    }

    // content objects
    function retrieve_content_objects_by_ids($condition, $offset = null, $max_objects = null, $order_by = null)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, $offset, $count, $order_property);
    }

    function count_content_objects_by_ids($condition)
    {
        return RepositoryDataManager :: get_instance()->count_content_objects($condition);
    }

    // internal items
    function retrieve_internal_items_by_application($condition, $offset = null, $max_objects = null, $order_by = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_internal_items_by_application($condition, $offset, $count, $order_property);
    }

    function retrieve_internal_item($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_internal_item($id);
    }

    function count_internal_items_by_application($condition)
    {
        return GradebookDataManager :: get_instance()->count_internal_items_by_application($condition);
    }

    function retrieve_categories_by_application($application)
    {
        return GradebookDataManager :: get_instance()->retrieve_categories_by_application($application);
    }

    // external items
    function retrieve_external_items($condition, $offset = null, $max_objects = null, $order_by = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_external_items($condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_item($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_external_item($id);
    }

    function count_external_items($condition)
    {
        return GradebookDataManager :: get_instance()->count_external_items($condition);
    }

    function retrieve_all_evaluations_on_external_publication($condition)
    {
        return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_external_publication($condition);
    }

    // evaluations
    function retrieve_all_evaluations_on_internal_publication($application, $publication_id, $offset = null, $max_objects = null, $order_by = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_internal_publication($application, $publication_id, $offset, $max_objects, $order_by);
    }

    // URL creation
    //***************
    function get_admin_browse_evaluation_format_types_link()
    {
        return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_EVALUATION_FORMATS));
    }

    function get_evaluation_format_editing_url($evaluation_format)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_EVALUATION_FORMAT, self :: PARAM_EVALUATION_FORMAT => $evaluation_format->get_id()));
    }

    function get_change_evaluation_format_activation_url($evaluation_format)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_FORMAT_ACTIVE_PROPERTY, self :: PARAM_EVALUATION_FORMAT => $evaluation_format->get_id()));
    }

    function get_evaluation_format_deleting_url()
    {
        return $this->get_url();
    }

    function get_internal_evaluations_on_publications_viewer_url($internal_item)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_EVALUATIONS_ON_PUBLICATION, self :: PARAM_PUBLICATION_APP => $internal_item->get_application(), self :: PARAM_PUBLICATION_ID => $internal_item->get_publication_id()));
    }

    function get_external_evaluations_on_publications_viewer_url($external_item)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_EVALUATIONS_ON_PUBLICATION, self :: PARAM_PUBLICATION_ID => $external_item->get_id()));
    }

    function get_edit_external_evaluation_url($external_item)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_EXTERNAL_EVALUATION, self :: PARAM_PUBLICATION_ID => $external_item->get_id()));
    }

    function get_delete_external_evaluation_url($external_item)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_EXTERNAL_EVALUATION, self :: PARAM_PUBLICATION_ID => $external_item->get_id()));
    }

    function get_publications_by_type_viewer_url($type, $the_application)
    {
        return $this->get_url(array(self :: PARAM_PUBLICATION_TYPE => $type, self :: PARAM_PUBLICATION_APP => $the_application));
    }

    function get_create_external_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID)));
    }

    function get_export_publication_url($publication_id)
    {
        //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_general_breadcrumbs()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('Gradebook')));
        $trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowsePublications')));
        $application = Request :: get(GradebookManager :: PARAM_PUBLICATION_APP);
        if ($application)
        {
            $url_params = array();
            $url_params[GradebookManager :: PARAM_ACTION] = GradebookManager :: ACTION_BROWSE_GRADEBOOK;
            $url_params[GradebookManager :: PARAM_PUBLICATION_APP] = $application;
            if (Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE))
                $url_params[GradebookManager :: PARAM_PUBLICATION_TYPE] = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
            $trail->add(new Breadcrumb($this->get_url($url_params), ucfirst($application)));
        }
        return $trail;
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
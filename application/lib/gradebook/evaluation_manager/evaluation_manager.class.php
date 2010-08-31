<?php
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';
require_once dirname(__FILE__) . '/evaluation_manager_interface.class.php';

class EvaluationManager extends SubManager
{
    const APPLICATION_NAME = 'evaluation';
    
    const PARAM_EVALUATION_ACTION = 'evaluation_action';
    const PARAM_EVALUATION_ID = 'evaluation_id';
    
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_PUBLISHER_ID = 'publisher_id';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    
    const ACTION_BROWSE = 'browser';
    const ACTION_CREATE = 'creator';
    const ACTION_UPDATE = 'updater';
    const ACTION_DELETE = 'deleter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
    
    const TYPE_INTERNAL_ITEM = 'internal_item';
    
    private $publisher_id;
    private $publication_id;
    private $trail;

    function EvaluationManager($parent)
    {
        parent :: __construct($parent);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/gradebook/evaluation_manager/component/';
    }

    function get_publisher_id()
    {
        return $this->get_parent()->get_publisher_id();
    }

    function get_publication_id()
    {
        return $this->get_parent()->get_publication_id();
    }

    // database
    function retrieve_all_evaluations_on_internal_publication($offset = null, $count = null, $order_property = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_internal_publication(Request :: get('application'), $this->get_publication_id(), $offset, $count, $order_property);
    }

    function count_all_evaluations_on_publication()
    {
        return GradebookDataManager :: get_instance()->count_all_evaluations_on_publication($this->get_publication_id());
    }

    function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation($condition, $offset, $count, $order_property);
    }

    function retrieve_evaluation($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation($id);
    }

    function retrieve_grade_evaluation($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_grade_evaluation($id);
    }

    function retrieve_internal_item_by_publication($application, $publication_id)
    {
        return GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($application, $publication_id);
    }

    function retrieve_all_active_evaluation_formats()
    {
        return GradebookDataManager :: get_instance()->retrieve_all_active_evaluation_formats();
    }

    function retrieve_evaluation_format($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation_format($id);
    }

    function retrieve_evaluation_ids_by_publication($application, $publication_id)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation_ids_by_publication($application, $publication_id);
    }

    function move_internal_to_external($application, $publication)
    {
        return GradebookDataManager :: get_instance()->move_internal_to_external($application, $publication);
    }

    //url creation
    function get_evaluation_editing_url($evaluation)
    {
        return $this->get_url(array(self :: PARAM_EVALUATION_ACTION => self :: ACTION_UPDATE, self :: PARAM_EVALUATION_ID => $evaluation->get_id()));
    }

    function get_evaluation_deleting_url($evaluation)
    {
        return $this->get_url(array(self :: PARAM_EVALUATION_ACTION => self :: ACTION_DELETE, self :: PARAM_EVALUATION_ID => $evaluation->get_id()));
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
        return self :: PARAM_EVALUATION_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>
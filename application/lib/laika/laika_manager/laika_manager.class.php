<?php
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/laika/laika_rights.class.php';
require_once dirname(__FILE__) . '/component/laika_calculated_result_browser/laika_calculated_result_browser_table.class.php';

class LaikaManager extends WebApplication
{
    const APPLICATION_NAME = 'laika';
    
    const PARAM_ATTEMPT_ID = 'attempt';
    const PARAM_USER_ID = 'user';
    const PARAM_SCALE_ID = 'scale';
    const PARAM_MAIL_SELECTED = 'mail';
    const PARAM_RECIPIENTS = 'recipient';
    const PARAM_GROUP_ID = 'group';
    
    const ACTION_VIEW_HOME = 'home';
    const ACTION_VIEW_RESULTS = 'view';
    const ACTION_TAKE_TEST = 'take';
    const ACTION_BROWSE_RESULTS = 'browse';
    const ACTION_RENDER_GRAPH = 'graph';
    const ACTION_MAIL_LAIKA = 'mail';
    const ACTION_BROWSE_USERS = 'user';
    const ACTION_VIEW_STATISTICS = 'statistics';
    const ACTION_VIEW_INFORMATION = 'info';

    public function LaikaManager($user)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    public function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_VIEW_RESULTS :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_TAKE_TEST :
                $component = $this->create_component('Taker');
                break;
            case self :: ACTION_BROWSE_RESULTS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_RENDER_GRAPH :
                $component = $this->create_component('Grapher');
                break;
            case self :: ACTION_MAIL_LAIKA :
                $component = $this->create_component('Mailer');
                break;
            case self :: ACTION_BROWSE_USERS :
                $component = $this->create_component('User');
                break;
            case self :: ACTION_VIEW_STATISTICS :
                $component = $this->create_component('Analyzer');
                break;
            case self :: ACTION_VIEW_INFORMATION :
                $component = $this->create_component('Informer');
                break;
            default :
                $component = $this->create_component('Home');
                break;
        }
        $component->run();
    }

    /**
     * @see Application::content_object_is_published()
     */
    public function content_object_is_published($object_id)
    {
        return false;
    }

    /**
     * @see Application::any_content_object_is_published()
     */
    public function any_content_object_is_published($object_ids)
    {
        return false;
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return null;
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        return null;
    }

    /**
     * @see Application::count_publication_attributes()
     */
    public function count_publication_attributes($type = null, $condition = null)
    {
        return 0;
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        return true;
    }
    
	function delete_content_object_publication($publication_id)
    {
    	return true;
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    public function update_content_object_publication_id($publication_attr)
    {
        return true;
    }

    /**
     * Inherited
     */
    function get_content_object_publication_locations($content_object)
    {
        return array();
    }

    function publish_content_object($content_object, $location)
    {
        return Translation :: get('PublicationCreated');
    }

    function retrieve_laika_question($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_question($id);
    }

    function retrieve_laika_questions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_questions($condition, $offset, $count, $order_property);
    }

    function has_taken_laika($user)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->has_taken_laika($user);
    }

    function retrieve_laika_scale($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_scale($id);
    }

    function retrieve_laika_scales($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_scales($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_cluster($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_cluster($id);
    }

    function retrieve_laika_clusters($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_clusters($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_result($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_result($id);
    }

    function retrieve_laika_results($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_results($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_calculated_result($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_calculated_result($id);
    }

    function retrieve_laika_calculated_results($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_calculated_results($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_table_calculated_results($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_table_calculated_results($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_answer($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_answer($id);
    }

    function retrieve_laika_answers($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_answers($condition, $offset, $count, $order_property);
    }

    function retrieve_laika_attempt($id)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_attempt($id);
    }

    function retrieve_laika_attempts($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_attempts($condition, $offset, $count, $order_property);
    }

    function count_laika_attempts($condition = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->count_laika_attempts($condition);
    }

    function count_laika_calculated_results($condition = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->count_laika_calculated_results($condition);
    }

    function count_laika_table_calculated_results($condition = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->count_laika_table_calculated_results($condition);
    }

    function retrieve_laika_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->retrieve_laika_users($condition, $offset, $count, $order_property);
    }

    function count_laika_users($condition = null)
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->count_laika_users($condition);
    }

    /**
     * Gets the user object for a given user
     * @param int $user_id
     * @return User
     */
    function get_user_info($user_id)
    {
        return UserDataManager :: get_instance()->retrieve_user($user_id);
    }

    function get_laika_attempt_viewing_url($laika_attempt)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS, self :: PARAM_ATTEMPT_ID => $laika_attempt->get_id()));
    }

    function get_laika_user_viewing_url($user)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS, self :: PARAM_USER_ID => $user->get_id()));
    }

    function get_group_statistics_viewing_url($group)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_STATISTICS, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_laika_calculated_result_attempt_viewing_url($laika_calculated_result)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS, self :: PARAM_ATTEMPT_ID => $laika_calculated_result->get_attempt_id()));
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[LaikaCalculatedResultBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
                case self :: PARAM_MAIL_SELECTED :
                    $this->set_action(self :: ACTION_MAIL_LAIKA);
                    $_GET[self :: PARAM_RECIPIENTS] = $selected_ids;
                    break;
            }
        }
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
}
?>
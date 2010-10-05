<?php
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';
require_once Path :: get_application_path() . 'laika/php/laika_rights.class.php';
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
    const ACTION_VIEW_RESULTS = 'viewer';
    const ACTION_TAKE_TEST = 'taker';
    const ACTION_BROWSE_RESULTS = 'browser';
    const ACTION_RENDER_GRAPH = 'grapher';
    const ACTION_MAIL_LAIKA = 'mailer';
    const ACTION_BROWSE_USERS = 'user';
    const ACTION_VIEW_STATISTICS = 'analyzer';
    const ACTION_VIEW_INFORMATION = 'informer';

    const DEFAULT_ACTION = self :: ACTION_VIEW_HOME;

    public function LaikaManager($user)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    static function publish_content_object($content_object, $location)
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
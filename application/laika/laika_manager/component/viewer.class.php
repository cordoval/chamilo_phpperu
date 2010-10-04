<?php
/**
 * $Id: viewer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../../laika_utilities.class.php';
require_once dirname(__FILE__) . '/laika_attempt_browser/laika_attempt_browser_table.class.php';

class LaikaManagerViewerComponent extends LaikaManager
{
    private $attempt;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewLaikaResult')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_VIEWER, LaikaRights :: TYPE_LAIKA_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $attempt = $this->get_laika_attempt();
        $user = $attempt->get_user();
        
        $trail->add(new Breadcrumb($this->get_url(), $user->get_fullname()));
        
        $this->display_header($trail);
        echo $this->get_view_html();
        $this->display_footer();
    }

    function get_view_html()
    {
        $attempt = $this->get_laika_attempt();
        
        $html = array();
        
        if (LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, 'mailer', LaikaRights :: TYPE_LAIKA_COMPONENT))
        {
            $html[] = $this->get_action_bar_html();
        }
        $html[] = '<br />';
        $html[] = '<div id="action_bar_browser">';
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/announcement.png);">';
        $html[] = '<div class="title">' . Translation :: get('ResultsFrom') . ' ' . DatetimeUtilities :: format_locale_date(Translation :: get('dateTimeFormatLong'), $attempt->get_date()) . '</div>';
        $html[] = '<div class="description">';
        $html[] = LaikaUtilities :: get_laika_results_html($attempt);
        $html[] = '</div>';
        $html[] = '</div>';
        
        $maximum_attempts = PlatformSetting :: get('maximum_attempts', LaikaManager :: APPLICATION_NAME);
        
        if ($maximum_attempts > 1)
        {
            $attempts_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $attempt->get_user_id());
            $attempts_count = $this->count_laika_attempts($attempts_condition);
            
            if ($attempts_count > 1)
            {
                $html[] = '<h3>' . Translation :: get('OtherLaikaResultsFromSameUser') . '</h3>';
                $table = new LaikaAttemptBrowserTable($this, array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME), $this->get_attempt_condition());
                $html[] = $table->as_html();
            }
        }
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function get_laika_attempt()
    {
        if (! isset($this->attempt))
        {
            $user = $this->get_user();
            $attempt_id = Request :: get('attempt');
            $user_id = Request :: get('user');
            
            $is_admin = LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, 'browser', LaikaRights :: TYPE_LAIKA_COMPONENT);
            
            if (isset($attempt_id))
            {
                $attempt_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_ID, $attempt_id);
                $attempt = $this->retrieve_laika_attempts($attempt_condition, 0, 1, new ObjectTableOrder(LaikaAttempt :: PROPERTY_DATE, SORT_DESC))->next_result();
                
                $attempt_user = $attempt->get_user_id();
                if ($attempt_user != $user->get_id() && ! $is_admin)
                {
                    $attempt_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
                    $attempt = $this->retrieve_laika_attempts($attempt_condition, 0, 1, new ObjectTableOrder(LaikaAttempt :: PROPERTY_DATE, SORT_DESC))->next_result();
                }
            }
            elseif (isset($user_id) && $is_admin)
            {
                $attempt_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user_id);
                $attempt = $this->retrieve_laika_attempts($attempt_condition, 0, 1, new ObjectTableOrder(LaikaAttempt :: PROPERTY_DATE, SORT_DESC))->next_result();
            }
            else
            {
                $attempt_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
                $attempt = $this->retrieve_laika_attempts($attempt_condition, 0, 1, new ObjectTableOrder(LaikaAttempt :: PROPERTY_DATE, SORT_DESC))->next_result();
            }
            
            $this->attempt = $attempt;
        }
        
        return $this->attempt;
    }

    function get_attempt_condition()
    {
        $attempt = $this->get_laika_attempt();
        
        $attempt_conditions = array();
        $attempt_conditions[] = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $attempt->get_user_id());
        $attempt_conditions[] = new NotCondition(new EqualityCondition(LaikaAttempt :: PROPERTY_ID, $attempt->get_id()));
        
        $attempt_condition = new AndCondition($attempt_conditions);
        
        return $attempt_condition;
    }

    function get_action_bar_html()
    {
        $attempt = $this->get_laika_attempt();
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('SendMail'), Theme :: get_common_image_path() . 'action_mail.png', $this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_MAIL_LAIKA, LaikaManager :: PARAM_RECIPIENTS => $attempt->get_user()->get_id()))));
        //$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('laika'));
        

        return $action_bar->as_html();
    }
}
?>
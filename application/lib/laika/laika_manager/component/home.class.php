<?php
/**
 * $Id: home.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_manager_component.class.php';
require_once dirname(__FILE__) . '/../../laika_utilities.class.php';
require_once dirname(__FILE__) . '/laika_attempt_browser/laika_attempt_browser_table.class.php';

class LaikaManagerHomeComponent extends LaikaManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Laika')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_HOME, 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail);
        echo $this->get_laika_home();
        $this->display_footer();
    }

    function get_laika_home()
    {
        $is_admin = LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_BROWSER, 'laika_component');
        $is_user = LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_TAKER, 'laika_component');
        
        if ($is_admin)
        {
            echo $this->get_laika_home_admin();
        }
        elseif ($is_user)
        {
            echo $this->get_laika_home_user();
        }
        else
        {
            $this->display_error_message(Translation :: get('NotAllowed'));
        }
    }

    function get_laika_home_user()
    {
        $html = array();
        $user = $this->get_user();
        
        // Personal Laika results
        $has_taken_laika = $this->has_taken_laika($user);
        if ($has_taken_laika)
        {
            $html[] = '<div style="float: right; width: 40%;">';
            $html[] = $this->get_laika_results();
            $html[] = '</div>';
        }
        
        // Introduction text for users
        $html[] = '<div style="float: left; width: 59%;">';
        $html[] = '<div class="block" style="background-image: url(' . Theme :: get_image_path() . 'block_laika.png);">';
        $html[] = '<div class="title">' . Translation :: get('TakeLaikaTest') . '</div>';
        $html[] = '<div class="description">';
        $html[] = Translation :: get('LaikaIntro');
        $html[] = '<div style="clear: both;"></div>';
        
        $maximum_attempts = PlatformSetting :: get('maximum_attempts', LaikaManager :: APPLICATION_NAME);
        
        if (($has_taken_laika && $maximum_attempts > 1) || ! $has_taken_laika)
        {
            $html[] = '<div style="text-align: center; clear: both; margin-bottom: 15px; margin-top: 15px;">';
            $html[] = '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_TAKE_TEST)) . '" class="button normal_button select_button">' . Translation :: get('StartLaika') . '</a>';
            $html[] = '</div>';
            $html[] = '<div style="clear: both;"></div>';
        }
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function get_laika_home_admin()
    {
        $html = array();
        $user = $this->get_user();
        
        $html[] = LaikaUtilities :: get_laika_admin_menu($this);
        $html[] = '<div id="tool_browser_right">';
        
        // Introduction text for admins
        $html[] = '<div>';
        $html[] = '<div class="block" style="background-image: url(' . Theme :: get_image_path() . 'block_laika.png);">';
        $html[] = '<div class="title">' . Translation :: get('LaikaFull') . '</div>';
        $html[] = '<div class="description">';
        
        $introduction = Translation :: get('LaikaAdminIntro');
        $introduction = str_replace('{LAIKA_CONTACT}', '', $introduction);
        $introduction = str_replace('{INSTITUTION}', PlatformSetting :: get('institution'), $introduction);
        
        $administrator_name = PlatformSetting :: get('administrator_firstname') . '&nbsp;' . PlatformSetting :: get('administrator_surname');
        
        $introduction = str_replace('{ADMINISTRATOR_NAME}', $administrator_name, $introduction);
        $introduction = str_replace('{ADMINISTRATOR_MAIL}', PlatformSetting :: get('administrator_email'), $introduction);
        
        $html[] = $introduction;
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        // Personal Laika Results
        $has_taken_laika = $this->has_taken_laika($user);
        if ($has_taken_laika)
        {
            $html[] = '<div>';
            $html[] = $this->get_laika_results();
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function get_laika_results()
    {
        $html = array();
        
        $user = $this->get_user();
        
        $attempt = $this->get_laika_attempt();
        
        $maximum_attempts = PlatformSetting :: get('maximum_attempts', LaikaManager :: APPLICATION_NAME);
        
        $html[] = '<div class="block" style="background-image: url(' . Theme :: get_image_path() . 'block_results.png);">';
        if ($maximum_attempts == 1)
        {
            $html[] = '<div class="title">' . Translation :: get('Results');
        }
        else
        {
            $html[] = '<div class="title">' . Translation :: get('MostRecentResults');
        }
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = LaikaUtilities :: get_laika_results_html($attempt);
        $html[] = '<div style="clear: both;"></div></div>';
        $html[] = '</div>';
        
        if ($maximum_attempts > 1)
        {
            $attempts_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
            $attempts_count = $this->count_laika_attempts($attempts_condition);
            
            if ($attempts_count > 1)
            {
                $html[] = '<div class="block" style="background-image: url(' . Theme :: get_image_path() . 'block_archive.png);">';
                $html[] = '<div class="title">' . Translation :: get('PreviousResults');
                $html[] = '</div>';
                $html[] = '<div class="description">';
                
                $table = new LaikaAttemptBrowserTable($this, array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME), $this->get_attempt_condition());
                $html[] = $table->as_html();
                $html[] = '<div style="clear: both;"></div></div>';
                $html[] = '</div>';
            }
        }
        
        return implode("\n", $html);
    }

    function get_attempt_condition()
    {
        $user = $this->get_user();
        
        $attempt_condition = new EqualityCondition(LaikaAttempt :: PROPERTY_USER_ID, $user->get_id());
        
        return $attempt_condition;
    }

    function get_laika_attempt()
    {
        return $this->retrieve_laika_attempts($this->get_attempt_condition(), 0, 1, new ObjectTableOrder(LaikaAttempt :: PROPERTY_DATE, SORT_DESC))->next_result();
    }
}
?>
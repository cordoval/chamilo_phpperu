<?php
namespace application\survey;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Breadcrumb;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\DynamicContentTab;
use user\User;
use common\libraries\InCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use user\UserDataManager;
use repository\content_object\survey\SurveyContextDataManager;
use repository\content_object\survey\SurveyTemplateUser;

class SurveyManagerParticipantBrowserComponent extends SurveyManager
{

    const TAB_PARTICIPANTS = 1;
    const TAB_INVITEES = 2;
    const TAB_NOT_PARTICIPANTS = 3;

    private $action_bar;
    private $pid;

    function run()
    {

        $this->pid = Request :: get(self :: PARAM_PUBLICATION_ID);
        $this->action_bar = $this->get_action_bar();

        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';

        echo '<div>';
        echo $this->get_tables();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tables()
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[self :: PARAM_PUBLICATION_ID] = $this->pid;

        $table = new SurveyParticipantBrowserTable($this, $parameters, $this->get_participant_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PARTICIPANTS, Translation :: get('Participants'), Theme :: get_image_path() . 'logo/16.png', $table->as_html()));

        $table = new SurveyUserBrowserTable($this, $parameters, $this->get_invitee_condition(), $this->pid, SurveyUserBrowserTable :: TYPE_INVITEES);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_INVITEES, Translation :: get('Invitees'), Theme :: get_image_path() . 'logo/16.png', $table->as_html()));

        $table = new SurveyUserBrowserTable($this, $parameters, $this->get_no_participant_condition(), $this->pid, SurveyUserBrowserTable :: TYPE_NO_PARTICIPANTS);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_NOT_PARTICIPANTS, Translation :: get('NoParticipants'), Theme :: get_image_path() . 'logo/16.png', $table->as_html()));

        $html[] = $tabs->render();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");

    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_PUBLICATION_ID] = $this->pid;

        $action_bar->set_search_url($this->get_url($parameters));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', array(), Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, SurveyRights :: LOCATION_PARTICIPANT_BROWSER, SurveyRights :: TYPE_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_subscribe_user_url($this->pid), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('SubscribeGroups'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_subscribe_group_url($this->pid), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('InviteUsers'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_invite_user_url($this->pid), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        	$action_bar->add_tool_action(new ToolbarItem(Translation :: get('SubscribeTemplateUsers'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_invite_template_user_url($this->pid), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
        }

        return $action_bar;
    }

    function get_participant_condition()
    {

        $query = $this->action_bar->get_query();
        if (! isset($query))
        {
            $query = Request :: get(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY);
        }
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);

        if (isset($query) && $query != '')
        {
            $user_conditions = array();
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $user_condition = new OrCondition($user_conditions);
            $users = UserDataManager :: get_instance()->retrieve_users($user_condition);
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }

            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME, '*' . $query . '*');
            $search_conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $user_ids);
            $conditions[] = new OrCondition($search_conditions);
        }

        return new AndCondition($conditions);
    }

    function get_invitee_condition()
    {

        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);

        $invited_users = array();
        $invited_users = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_PARTICIPATE, $publication_id, SurveyRights :: TYPE_PUBLICATION);

        $condition = null;
        if (count($invited_users) > 0)
        {

            $condition = new InCondition(User :: PROPERTY_ID, $invited_users);

        }
        else
        {
            $condition = new EqualityCondition(User :: PROPERTY_ID, 0);
        }

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
        }

        if ($or_condition)
        {
            $conditions = array($condition, $or_condition);
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

    function get_no_participant_condition()
    {

        $survey_pub_id = Request :: get(self :: PARAM_PUBLICATION_ID);

        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_pub_id);

        $survey = $survey_publication->get_publication_object();

        $context_template = $survey->get_context_template_for_level();

        if ($context_template)
        {

            $invited_users = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_VIEW, $survey_pub_id, SurveyRights :: TYPE_PUBLICATION);
            $cdm = SurveyContextDataManager :: get_instance();

            if (count($invited_users) > 0)
            {
                $condition = new InCondition(SurveyTemplateUser :: PROPERTY_USER_ID, $invited_users, SurveyTemplateUser :: get_table_name());

            }
            else
            {
                return $condition = new EqualityCondition(User :: PROPERTY_ID, 0);
            }

            $templates = $cdm->retrieve_survey_template_users($context_template->get_type(), $condition);
            $template_users = array();
            while ($template = $templates->next_result())
            {
                $template_users[] = $template->get_user_id();
            }

            $no_participant_users = array_diff($invited_users, $template_users);

            $condition = null;
            if (count($no_participant_users) > 0)
            {

                $condition = new InCondition(User :: PROPERTY_ID, $no_participant_users);

            }
            else
            {
                $condition = new EqualityCondition(User :: PROPERTY_ID, 0);
            }

            $query = $this->action_bar->get_query();

            if (isset($query) && $query != '')
            {
                $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
                $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
                $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
                $or_condition = new OrCondition($or_conditions);
            }

            if ($or_condition)
            {
                $conditions = array($condition, $or_condition);
                $condition = new AndCondition($conditions);
            }

            return $condition;
        }
        else
        {
            return $condition = new EqualityCondition(User :: PROPERTY_ID, 0);
        }

    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>
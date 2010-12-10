<?php
namespace application\survey;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DynamicTabsRenderer;
use common\extensions\invitation_manager\InvitationManager;
use common\extensions\invitation_manager\InvitationSupport;

class SurveyManagerInviterComponent extends SurveyManager implements InvitationSupport
{
    private $publication_id;
    private $publication;

    function run()
    {
        $this->publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        $this->publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);
        $invitation_manager = new InvitationManager($this);
        $invitation_manager->run();
    }

    function get_url_parameters()
    {
        $survey_id = $this->publication->get_content_object_id();
        return array(self :: PARAM_APPLICATION => self :: APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_TAKE, self :: PARAM_PUBLICATION_ID => $this->publication_id, self :: PARAM_SURVEY_ID => $survey_id);
    }

    function process_existing_users(array $user_ids = array())
    {
    	return true;
    }

    function get_location_rights_ids()
    {
        $location_right_ids = array();
        $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($this->publication_id, SurveyRights :: TYPE_PUBLICATION);
        $location_right_ids[$location_id] = array(SurveyRights :: RIGHT_INVITE);
        return $location_right_ids;
    }

    function get_expiration_date()
    {
        return $this->publication->get_to_date();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication(Request :: get(self :: PARAM_PUBLICATION_ID));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $publication->get_type())), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>
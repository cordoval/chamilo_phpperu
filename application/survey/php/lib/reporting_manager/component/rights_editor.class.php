<?php
namespace application\survey;

use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\Request;

use common\extensions\rights_editor_manager\RightsEditorManager;

class SurveyReportingManagerRightsEditorComponent extends SurveyReportingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $publication_rel_reporting_template_ids = Request :: get(self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID);

        $this->set_parameter(self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID, $publication_rel_reporting_template_ids);

        if ($publication_rel_reporting_template_ids && ! is_array($publication_rel_reporting_template_ids))
        {
            $publication_rel_reporting_template_ids = array($publication_rel_reporting_template_ids);
        }

        $locations = array();

        $publication_id = 0;

        foreach ($publication_rel_reporting_template_ids as $publication_rel_reporting_template_id)
        {

            $publication_rel_reporting_template = SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registration_by_id($publication_rel_reporting_template_id);

            if ($this->get_user()->is_platform_admin() || $publication_rel_reporting_template->get_owner_id() == $this->get_user_id())
            {
                $publication_id = $publication_rel_reporting_template->get_publication_id();
                $locations[] = SurveyRights :: get_location_by_identifier_from_surveys_subtree($publication_rel_reporting_template_id, SurveyRights :: TYPE_REPORTING_TEMPLATE_REGISTRATION);
            }
        }

        $manager = new RightsEditorManager($this, $locations);

        $user_ids = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_REPORTING, $publication_id, SurveyRights :: TYPE_PUBLICATION);
        if (count($user_ids) > 0)
        {
            $manager->limit_users($user_ids);
        }
        else
        {
            $manager->limit_users(array(0));
        }

        $manager->set_types(array(RightsEditorManager :: TYPE_USER));
        $manager->run();
    }

    function get_available_rights()
    {
        return SurveyRights :: get_available_rights_for_reporting_template_registrations();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE,
                SurveyManager :: PARAM_PUBLICATION_ID => Request :: get(SurveyManager :: PARAM_PUBLICATION_ID))), Translation :: get('BrowseReportingTemplates')));
    }

    function get_additional_parameters()
    {
        return array(SurveyManager :: PARAM_PUBLICATION_ID, self :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID);
    }
}
?>
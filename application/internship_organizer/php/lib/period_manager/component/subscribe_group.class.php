<?php
namespace application\internship_organizer;

use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\DynamicTabsRenderer;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/period_subscribe_group_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/browser.class.php';

class InternshipOrganizerPeriodManagerSubscribeGroupComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $period_id = Request :: get(self :: PARAM_PERIOD_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_GROUP_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period = $this->retrieve_period($period_id);
        
        $form = new InternshipOrganizerPeriodSubscribeGroupForm($period, $this->get_url(array(self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_period_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelGroupsCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_GROUPS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodRelGroupsNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerBrowserComponent :: TAB_GROUPS));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseContextRegistrations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID))), Translation :: get('ViewContextRegistration')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_GROUPS)), Translation :: get('ViewSurveyContext')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID, self :: PARAM_CONTEXT_ID);
    }

}
?>
<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/organisation_subscribe_users_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/viewer.class.php';

class InternshipOrganizerOrganisationManagerSubscribeUserComponent extends InternshipOrganizerOrganisationManager
{
   
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $organisation_id = Request :: get(self :: PARAM_ORGANISATION_ID);
		$organisation = $this->retrieve_organisation($organisation_id);
        
        $form = new InternshipOrganizerOrganisationSubscribeUsersForm($organisation, $this->get_url(array(self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_organisation_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationRelUsersCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_USERS));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationRelUsersNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_USERS));
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
        $breadcrumbtrail->add_help('organisation subscribe users');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_USERS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }

}
?>
<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';

class InternshipOrganizerOrganisationManagerLocationDeleterComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[self :: PARAM_LOCATION_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $location = $this->retrieve_location($id);
                $organisation_id = $location->get_organisation_id();
                
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $id, InternshipOrganizerRights :: TYPE_LOCATION))
                {
                    if (! $location->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerLocationNotDeleted';
                }
                else
                {
                    $message = 'Selected{InternshipOrganizerLocationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerLocationDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerLocationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerLocationsSelected')));
        }
    }
}
?>
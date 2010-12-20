<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
//require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'moment_manager/component/viewer.class.php';


class InternshipOrganizerAppointmentManagerAppointmentDeleterComponent extends InternshipOrganizerAppointmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[self :: PARAM_APPOINTMENT_ID];
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {

                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $id, InternshipOrganizerRights :: TYPE_APPOINTMENT))
                {
                    $appointment = $this->retrieve_appointment($id);
                    $moment_id = $appointment->get_moment_id();
                    if (! $appointment->delete())
                    {
                        $failures ++;
                    }
                }

            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAppointmentNotDeleted';
                }
                else
                {
                    $message = 'Selected{InternshipOrganizerAppointmentsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedInternshipOrganizerAppointmentDeleted';
                }
                else
                {
                    $message = 'SelectedInternshipOrganizerAppointmentsDeleted';
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT,
                    self :: PARAM_MOMENT_ID => $moment_id,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAppointmentManagerBrowserComponent :: TAB_APPOINTMENTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerAppointmentsSelected')));
        }
    }
}
?>
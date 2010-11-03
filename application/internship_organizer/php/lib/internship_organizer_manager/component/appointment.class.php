<?php
namespace application\internship_organizer;


class InternshipOrganizerManagerAppointmentComponent extends InternshipOrganizerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerAppointmentManager :: launch($this);
    }
}
?>
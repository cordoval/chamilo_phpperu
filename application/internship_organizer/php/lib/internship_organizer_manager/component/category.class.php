<?php
namespace application\internship_organizer;

use common\libraries\DelegateComponent;

class InternshipOrganizerManagerCategoryComponent extends InternshipOrganizerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerCategoryManager :: launch($this);
    }
}
?>
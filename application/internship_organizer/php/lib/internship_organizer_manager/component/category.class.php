<?php

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
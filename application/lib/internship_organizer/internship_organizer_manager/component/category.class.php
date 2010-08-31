<?php

class InternshipOrganizerManagerCategoryComponent extends InternshipOrganizerManager
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
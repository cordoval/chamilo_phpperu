<?php

class InternshipOrganizerManagerCategoryComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new InternshipOrganizerCategoryManager($this);
        $category_manager->run();
    }
}
?>
<?php

class InternshipOrganizerManagerCategoryComponent extends InternshipOrganizerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new InternshipOrganizerCategoryManager($this->get_parent());
        $category_manager->run();
    }
}
?>
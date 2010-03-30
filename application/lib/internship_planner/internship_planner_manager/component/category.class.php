<?php

class InternshipPlannerManagerCategoryComponent extends InternshipPlannerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new InternshipPlannerCategoryManager($this->get_parent());
        $category_manager->run();
    }
}
?>
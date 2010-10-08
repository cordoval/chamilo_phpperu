<?php
class GroupManagerUsageManagerComponent extends GroupManager implements AdministrationComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->display_header();
        $this->display_footer();
    }
}
?>
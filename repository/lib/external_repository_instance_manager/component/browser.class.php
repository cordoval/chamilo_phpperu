<?php
class ExternalRepositoryInstanceManagerBrowserComponent extends ExternalRepositoryInstanceManager
{

    function run()
    {

        $this->display_header();
        echo 'Instance manager goes here !';
        $this->display_footer();
    }
}
?>
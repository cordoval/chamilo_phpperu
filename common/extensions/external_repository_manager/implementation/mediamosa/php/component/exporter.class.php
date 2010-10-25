<?php
/**
 * Description of importerclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerExporterComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        $this->display_header();
        echo Translation :: get('NotAvailable');
        $this->display_footer();
    }
}
?>

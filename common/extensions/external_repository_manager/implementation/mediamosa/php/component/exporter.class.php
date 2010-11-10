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
        echo Translation :: get('NotAvailable', null, Utilities :: COMMON_LIBRARIES);
        $this->display_footer();
    }
}
?>

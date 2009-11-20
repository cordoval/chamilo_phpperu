<?php

require_once dirname(__FILE__) . '/external_repository_export_component.class.php';

class RepositoryManagerExternalRepositoryExportListObjectsComponent extends RepositoryManagerExternalRepositoryExportComponent
{
    
    public function run()
    {
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb(null, Translation :: translate('ExternalExport')));
        
        
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE)), Translation :: translate('ExternalExport')));
        $this->display_header($trail, false, true);
        //$trail->add(new Breadcrumb(null, $export->get_title()));

        //$form = new ExternalExportBrowserForm(Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID), '', $this->get_catalogs());
        //$form->display();
        
        $this->display_footer();

    }
    
}
?>
<?php
/**
 * $Id: external_repository_export_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
require_once dirname(__FILE__) . '/external_repository_export_component.class.php';

class RepositoryManagerExternalRepositoryExportBrowserComponent extends RepositoryManagerExternalRepositoryExportComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail(false);
    
        $content_object = $this->get_content_object_from_params();
        
        if(isset($content_object))
        {
            if(!$this->check_content_object_from_params())
            {
                throw new Exception('You are not allowed to export this object');
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
            }
        }
        
        /*
         * Header links
         */
        $trail->add(new Breadcrumb(null, Translation :: translate('ExternalRepository')));
        $this->display_header($trail, false, true);
        
        /*
         * Page content
         */
        $form = new ExternalExportBrowserForm($content_object, '', $this->get_catalogs());
        $form->display();
        
        $this->display_footer();
    }

}

?>
<?php
/**
 * $Id: external_repository_repository_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerExternalRepositoryBrowserComponent extends RepositoryManagerExternalRepositoryComponent
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
    
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
        $trail->add(new Breadcrumb(null, Translation :: get('ExternalRepositoryList')));
        $this->display_header($trail, false, true);
        
        /*
         * Page content
         */
        $form = new ExternalRepositoryBrowserForm($content_object, '', $this->get_catalogs());
        $form->display();
        
        $this->display_footer();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_complex_builder');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }

}

?>
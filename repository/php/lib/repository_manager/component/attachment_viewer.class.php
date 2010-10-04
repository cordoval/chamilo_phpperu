<?php
/**
 * $Id: attachment_viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerAttachmentViewerComponent extends RepositoryManager
{

    function run()
    {
        $object_id = Request :: get('object');
        
        if ($object_id)
        {
            $this->display_header(null, false, false);
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header(null, false, true);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('repository_attachment_viewer');
    }
}
?>
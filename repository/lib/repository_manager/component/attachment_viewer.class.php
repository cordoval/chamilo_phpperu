<?php
/**
 * $Id: attachment_viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerAttachmentViewerComponent extends RepositoryManager
{

    function run()
    {
        /*if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository general');
        
        $object_id = Request :: get('object');
        
        if ($object_id)
        {
            $trail->add(new Breadcrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
            $this->display_header($trail, false, false);
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header($trail, false, true);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    
    }
}
?>
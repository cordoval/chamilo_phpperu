<?php
/**
 * $Id: attachment_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
/**
 * @author Michael Kyndt
 */

class ComplexDisplayAttachmentViewerComponent extends ComplexDisplayComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->get_parent()->get_parent()->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses general');
        
        $object_id = Request :: get('object_id');
        if ($object_id)
        {
            $trail->add(new Breadcrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
            $this->display_header($trail, true);
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header($trail, true);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    
    }
}
?>
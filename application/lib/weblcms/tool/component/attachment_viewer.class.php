<?php
/**
 * $Id: attachment_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';

class ToolAttachmentViewerComponent extends ToolComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses general');
        
        $object_id = Request :: get('object_id');
        if ($object_id)
        {
            $trail->add(new Breadcrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
            $this->display_header();
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header();
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    
    }
}
?>
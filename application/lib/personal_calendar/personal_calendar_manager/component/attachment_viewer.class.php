<?php
/**
 * $Id: attachment_viewer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';

class PersonalCalendarManagerAttachmentViewerComponent extends PersonalCalendarManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('personal calender general');
        
        $object_id = Request :: get('object');
        
        if ($object_id)
        {
            $trail->add(new Breadcrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
            $this->display_header($trail);
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header($trail);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    
    }
}
?>
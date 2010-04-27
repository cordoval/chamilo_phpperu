<?php
/**
 * $Id: attachment_viewer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 */
require_once dirname(__FILE__) . '/../personal_messenger_manager.class.php';
require_once dirname(__FILE__) . '/publication_browser/publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../../personal_messenger_menu.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';

class PersonalMessengerManagerAttachmentViewerComponent extends PersonalMessengerManager
{
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPersonalMessageAttachments')));
        $trail->add_help('personal messenger general');
        
        $id = Request :: get(PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID);
        
        if ($id)
        {
            $this->publication = $this->retrieve_personal_message_publication($id);
            $publication = $this->publication;
            if ($this->get_user_id() != $publication->get_user())
            {
                $this->display_header($trail);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $this->display_header($trail);
            echo $this->get_publication_as_html();
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPersonalMessageSelected')));
        }
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $message = $publication->get_publication_object();
        $html = array();
        
        if ($message->supports_attachments())
        {
            $attachments = $message->get_attached_content_objects();
            if (count($attachments))
            {
                Utilities :: order_content_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $display = ContentObjectDisplay :: factory($attachment);
                    $html[] = $display->get_full_html();
                    //					$html[] = '<div class="content_object" style="background-image: url('.Theme :: get_common_image_path().'content_object/'.$attachment->get_icon_name().'.png);">';
                //					$html[] = '<div class="title">'. $attachment->get_title() .'</div>';
                //					$html[] = $attachment->get_description();
                //					$html[] = '</div>';
                }
            }
        }
        
        return implode("\n", $html);
    }
}
?>
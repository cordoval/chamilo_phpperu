<?php
/**
 * $Id: viewer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../personal_messenger_manager.class.php';
require_once dirname(__FILE__) . '/../personal_messenger_manager_component.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';

class PersonalMessengerManagerViewerComponent extends PersonalMessengerManagerComponent
{
    private $folder;
    private $publication;
    private $actionbar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->folder = $this->get_folder();
        
        $id = Request :: get(PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID);
        
        if ($id)
        {
            $this->publication = $this->retrieve_personal_message_publication($id);
            $publication = $this->publication;
            
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES)), Translation :: get('MyPersonalMessenger')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES)), Translation :: get(ucfirst($this->folder))));
            $trail->add(new Breadcrumb($this->get_url(), $publication->get_publication_object()->get_title()));
            $trail->add_help('personal messenger general');
            
            if ($this->get_user_id() != $publication->get_user())
            {
                $this->display_header($trail);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            if ($publication->get_status() == 1)
            {
                $publication->set_status(0);
                $publication->update();
            }
            
            $this->action_bar = $this->get_action_bar($publication);
            
            $this->display_header($trail);
            echo $this->action_bar->as_html();
            echo '<div class="clear"></div><br />';
            echo $this->get_publication_as_html();
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPersonalMessageSelected')));
        }
    }

    function get_action_bar($personal_message)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        if ($this->folder == PersonalMessengerManager :: ACTION_FOLDER_INBOX)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Reply'), Theme :: get_common_image_path() . 'action_reply.png', $this->get_publication_reply_url($personal_message), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $message = $publication->get_publication_object();
        $html = array();
        
        $sender = $publication->get_publication_sender();
        $recipient = $publication->get_publication_recipient();
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/description.png);">';
        $html[] = '<div class="title">' . Translation :: get('Data') . '</div>';
        $html[] = '<div class="description">';
        
        if ($sender)
        {
            $html[] = '<b>' . Translation :: get('MessageFrom') . '</b>:&nbsp;' . $sender->get_firstname() . '&nbsp;' . $sender->get_lastname() . '<br />';
        }
        else
        {
            $html[] = '<b>' . Translation :: get('MessageFrom') . '</b>:&nbsp;' . Translation :: get('SenderUnknown') . '<br />';
        }
        
        if ($recipient)
        {
            $html[] = '<b>' . Translation :: get('MessageTo') . '</b>:&nbsp;' . $recipient->get_firstname() . '&nbsp;' . $recipient->get_lastname() . '<br />';
        }
        else
        {
            $html[] = '<b>' . Translation :: get('MessageTo') . '</b>:&nbsp;' . Translation :: get('RecipientUnknown') . '<br />';
        }
        
        $html[] = '<b>' . Translation :: get('MessageDate') . '</b>:&nbsp;' . Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $publication->get_published()) . '<br />';
        $html[] = '<b>' . Translation :: get('MessageSubject') . '</b>:&nbsp;' . $message->get_title();
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/personal_message.png);">';
        $html[] = '<div class="title">' . Translation :: get('Message') . '</div>';
        $html[] = '<div class="description">' . $message->get_description() . '</div>';
        $html[] = '</div>';
        
        if ($message->supports_attachments())
        {
            $attachments = $message->get_attached_content_objects();
            if (count($attachments))
            {
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments')) . '</div>';
                $html[] = '<ul class="attachments_list">';
                $html[] = Utilities :: add_block_hider();
                Utilities :: order_content_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li class="personal_message_attachment"><div style="float: left;"><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $attachment->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) . '"/></div><div style="float: left;">&nbsp;' . $attachment->get_title() . '&nbsp;</div>';
                    $html[] = Utilities :: build_block_hider($attachment->get_id(), 'Attachment');
                    
                    $display = ContentObjectDisplay :: factory($attachment);
                    $html[] = $display->get_full_html();
                    
                    $html[] = Utilities :: build_block_hider();
                    //$html[] = '<div style="clear: both;">&nbsp;</div>';
                    $html[] = '</li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
            }
        }
        
        return implode("\n", $html);
    }
}
?>
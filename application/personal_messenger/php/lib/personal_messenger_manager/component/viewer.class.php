<?php
namespace application\personal_messenger;

use common\libraries\AttachmentSupport;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Path;
use common\libraries\DatetimeUtilities;
use common\libraries\Utilities;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;

use repository\ContentObjectDisplay;
use repository\ContentObject;
use repository\RepositoryManager;
/**
 * $Id: viewer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

class PersonalMessengerManagerViewerComponent extends PersonalMessengerManager
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

            if ($this->get_user_id() != $publication->get_user())
            {
                $this->display_header();
                Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
                exit();
            }

            if ($publication->get_status() == 1)
            {
                $publication->set_status(0);
                $publication->update();
            }

            $this->action_bar = $this->get_action_bar($publication);

            $this->display_header();
            if ($this->folder == PersonalMessengerManager :: FOLDER_INBOX)
            {
                echo $this->action_bar->as_html();
            }
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
        if ($this->folder == PersonalMessengerManager :: FOLDER_INBOX)
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

        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path() . 'description.png);">';
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

        $html[] = '<b>' . Translation :: get('MessageDate') . '</b>:&nbsp;' . DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $publication->get_published()) . '<br />';
        $html[] = '<b>' . Translation :: get('MessageSubject') . '</b>:&nbsp;' . $message->get_title();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path() . 'personal_message.png);">';
        $html[] = '<div class="title">' . Translation :: get('Message') . '</div>';
        $html[] = '<div class="description">' . $message->get_description() . '</div>';
        $html[] = '</div>';

        if ($message instanceof AttachmentSupport)
        {
            $attachments = $message->get_attached_content_objects();
            if (count($attachments))
            {
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments', null, 'repository')) . '</div>';
                $html[] = '<ul class="attachments_list">';
                $html[] = Utilities :: add_block_hider();
                Utilities :: order_content_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $url = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=attachment_viewer&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $attachment->get_id();
                    $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                    $html[] = '<li><a href="#" onClick="' . $url . '"><img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($attachment->get_type())) . 'logo/' . Theme :: ICON_MINI . '.png" alt="' . htmlentities(Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace($attachment->get_type()))) . '"/> ' . $attachment->get_title() . '</a></li>';

                //                    $html[] = '<li class="personal_message_attachment"><div style="float: left;"><img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($attachment->get_type())) . 'logo/' . Theme :: ICON_MINI . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName', null , 'repository\\content_object\\'.$attachment->get_type())) . '"/></div><div style="float: left;">&nbsp;' . $attachment->get_title() . '&nbsp;</div>';
                //                    $html[] = Utilities :: build_block_hider($attachment->get_id(), 'Attachment');
                //
                //                    $display = ContentObjectDisplay :: factory($attachment);
                //                    $html[] = $display->get_full_html();
                //
                //                    $html[] = Utilities :: build_block_hider();
                //                    //$html[] = '<div style="clear: both;">&nbsp;</div>';
                //                    $html[] = '</li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
            }
        }

        return implode("\n", $html);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Application :: PARAM_ACTION => self :: ACTION_BROWSE_MESSAGES)), Translation :: get('PersonalMessengerManagerBrowserComponent')));
        $breadcrumbtrail->add_help('personal_messenger_viewer');
    }

    function get_additional_parameters()
    {
        return array(
                self :: PARAM_PERSONAL_MESSAGE_ID);
    }
}
?>
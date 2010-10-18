<?php
/**
 * $Id: pm_publication_browser_table_cell_renderer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component.pm_publication_browser
 */
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'pm_publication_table/default_pm_publication_table_cell_renderer.class.php';
/**
 * Cell render for the personal message publication browser table
 */
class PmPublicationBrowserTableCellRenderer extends DefaultPmPublicationTableCellRenderer
{
    /**
     * The personal messenger browser component
     */
    private $browser;

    /**
     * Constructor
     * @param PersonalMessengerManagerBrowserComponent $browser
     */
    function PmPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $personal_message)
    {
        if ($column === PmPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($personal_message);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case PersonalMessagePublication :: PROPERTY_PUBLISHED :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $personal_message->get_published());
                break;
            case PersonalMessagePublication :: PROPERTY_STATUS :
                if ($personal_message->get_status() == 1)
                {
                    return '<img src="' . Theme :: get_common_image_path() . 'content_object/personal_message_new.png" />';
                }
                else
                {
                    return '<img src="' . Theme :: get_common_image_path() . 'content_object/personal_message.png" />';
                }
                break;
            case PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE :
                $title = parent :: render_cell($column, $personal_message);
                $title_short = $title;
                //				if(strlen($title_short) > 53)
                //				{
                //					$title_short = mb_substr($title_short,0,50).'&hellip;';
                //				}
                $title_short = Utilities :: truncate_string($title_short, 53, false);
                return '<a href="' . htmlentities($this->browser->get_publication_viewing_url($personal_message)) . '" title="' . $title . '">' . $title_short . '</a>';
                break;
        }
        return parent :: render_cell($column, $personal_message);
    }

    /**
     * Gets the action links to display
     * @param PersonalMessage $personal_message The personal message for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($personal_message)
    {
        $delete_url = $this->browser->get_publication_deleting_url($personal_message);
        
    	$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$delete_url,
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
   		if ($this->browser->get_folder() == PersonalMessengerManager :: FOLDER_INBOX)
        {
            $reply_url = $this->browser->get_publication_reply_url($personal_message);
            
            $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Reply'),
        		Theme :: get_common_image_path() . 'action_reply.png',
        		$reply_url,
        		ToolbarItem :: DISPLAY_ICON
       		));
        }
        else
        {
	        /*$toolbar->add_item(new ToolbarItem(
	        		Translation :: get('ReplyNa'),
	        		Theme :: get_common_image_path() . 'action_reply_na.png',
	        		null,
	        		ToolbarItem :: DISPLAY_ICON
	        ));*/
        }
        
        return $toolbar->as_html();
    }
}
?>
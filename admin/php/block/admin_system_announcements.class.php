<?php

/**
 * @package admin.block
 * $Id: admin_system_announcements.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
class AdminSystemAnnouncements extends AdminBlock
{

    /**
     * Runs this component and displays its output.
     * This component is only meant for use within the home-component and not as a standalone item.
     */
    function run()
    {
        return $this->as_html();
    }

    function as_html()
    {
        $configuration = $this->get_configuration();
        $show_when_empty = $configuration['show_when_empty'];
        $html = array();
        
        $announcements = $this->get_parent()->retrieve_system_announcement_publications();
        
        if ($announcements->size() > 0)
        {
            $html[] = $this->display_header();
            $html[] = $this->get_system_announcements($announcements);
            $html[] = $this->display_footer();
        }
        else
        {
            if ($show_when_empty)
            {
                $html[] = $this->display_header();
                $html[] = Translation :: get('NoSystemAnnouncementsCurrently');
                $html[] = $this->display_footer();
            }
        }
        
        return implode("\n", $html);
    }

    function get_system_announcements($announcements)
    {
        $html = array();
        
        $html[] = '<ul style="list-style: none; margin: 0px; padding: 0px;">';
        
        if ($announcements->size() == 0)
        {
            $html[] = '<li style="margin-bottom: 2px;">' . Translation :: get('NoNewSystemAnnouncements') . '</a></li>';
        }
        
        while ($announcement = $announcements->next_result())
        {
        	if ($announcement->is_visible_for_target_users())
        	{
            	$object = $announcement->get_publication_object();
            	$html[] = '<li style="margin-bottom: 2px;"><img style="vertical-align: middle;" src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $object->get_icon_name() . '.png" />&nbsp;&nbsp;<a href="' . $this->get_parent()->get_link(array(Application :: PARAM_ACTION => AdminManager :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT, AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $announcement->get_id())) . '">' . $object->get_title() . '</a></li>';
        	}
        }
        
        $html[] = '</ul>';
        
        return implode("\n", $html);
    }

    /*function is_editable()
	{
		return false;
	}*/
    
    function is_hidable()
    {
        return false;
    }

    function is_deletable()
    {
        return false;
    }
}
?>
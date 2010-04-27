<?php
/**
 * $Id: browser.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.linker_manager.component
 */
require_once dirname(__FILE__) . '/../linker_manager.class.php';

/**
 * linker component which allows the user to browse his links
 */
class LinkerManagerBrowserComponent extends LinkerManager
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Links')));
        
        $this->display_header($trail);
        
        echo '<a href="' . $this->get_create_link_url() . '">' . Translation :: get('Create') . '</a>';
        echo '<br /><br />';
        
        $links = $this->retrieve_links();
        while ($link = $links->next_result())
        {
            echo '<div style="border: 1px solid grey; padding: 5px;">';
            echo Translation :: get('Title') . ': ' . $link->get_name();
            echo '<br />' . Translation :: get('Description') . ': ' . $link->get_description();
            echo '<br /><a href="' . $link->get_url() . '">' . $link->get_url() . '</a>';
            echo '<br /><a href="' . $this->get_update_link_url($link) . '">' . Translation :: get('Update') . '</a>';
            echo ' | <a href="' . $this->get_delete_link_url($link) . '">' . Translation :: get('Delete') . '</a>';
            echo '</div><br /><br />';
        }
        
        $this->display_footer();
    }

}
?>
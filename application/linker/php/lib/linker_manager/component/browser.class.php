<?php
namespace application\linker;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
/**
 * $Id: browser.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.linker_manager.component
 */
/**
 * linker component which allows the user to browse his links
 */
class LinkerManagerBrowserComponent extends LinkerManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Links')));
        
        $this->display_header($trail);
        
        echo '<a href="' . $this->get_create_link_url() . '">' . Translation :: get('Create', null, Utilities::COMMON_LIBRARIES) . '</a>';
        echo '<br /><br />';
        
        $links = $this->retrieve_links();
        while ($link = $links->next_result())
        {
            echo '<div style="border: 1px solid grey; padding: 5px;">';
            echo Translation :: get('Title', null, Utilities::COMMON_LIBRARIES) . ': ' . $link->get_name();
            echo '<br />' . Translation :: get('Description', null, Utilities::COMMON_LIBRARIES) . ': ' . $link->get_description();
            echo '<br /><a href="' . $link->get_url() . '">' . $link->get_url() . '</a>';
            echo '<br /><a href="' . $this->get_update_link_url($link) . '">' . Translation :: get('Update', null, Utilities::COMMON_LIBRARIES) . '</a>';
            echo ' | <a href="' . $this->get_delete_link_url($link) . '">' . Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES) . '</a>';
            echo '</div><br /><br />';
        }
        
        $this->display_footer();
    }

}
?>
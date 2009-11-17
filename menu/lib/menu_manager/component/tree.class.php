<?php
/**
 * $Id: tree.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */

class MenuManagerTreeComponent extends MenuManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        // TODO: Develop further, still in experimental stage.
        $root_item_condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, 0);
        $root_items = $this->retrieve_navigation_items($root_item_condition);
        
        $this_section = Header :: get_section();
        $html = array();
        
        while ($root_item = $root_items->next_result())
        {
            $application = $root_item->get_application();
            
            if (isset($application))
            {
                $url = 'index_' . $root_item->get_application() . '.php';
            }
            else
            {
                $url = 'index.php';
            }
            
            $subitem_condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $root_item->get_id());
            $subitems = $this->retrieve_navigation_items($subitem_condition);
            $count = $subitems->size();
            if ($count > 0)
            {
                $is_current = false;
                $html_sub = array();
                
                if ($count > 0)
                {
                    $html_sub[] = '<a href="#">' . $root_item->get_title() . '</a><br />';
                    
                    while ($subitem = $subitems->next_result())
                    {
                        $html_sub[] = '--- <a href="index_' . $subitem->get_application() . '.php">' . $subitem->get_title() . '</a><br />';
                    }
                }
                $html[] = implode("\n", $html_sub);
            }
            else
            {
                $html[] = '<a href="index_' . $root_item->get_application() . '.php" ' . ($this_section == $root_item->get_application() ? 'class="current"' : '') . '>' . $root_item->get_title() . '</a><br />';
            }
        }
        
        // Repository link for admins
        $user = $this->get_user();
        if (isset($user) && $user->is_platform_admin())
        {
            $html[] = '<a href="index_repository_manager.php" ' . ($this_section == 'repository_manager' ? 'class="current"' : '') . '>Repository</a><br />';
        }
        
        return implode("\n", $html);
    }
}
?>
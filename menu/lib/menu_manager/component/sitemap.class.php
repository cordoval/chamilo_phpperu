<?php
/**
 * $Id: sitemap.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */

class MenuManagerSitemapComponent extends MenuManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $root_item_condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, 0);
        $root_items = $this->retrieve_navigation_items($root_item_condition);
        
        $this_section = Header :: get_section();
        $html = array();
        
        while ($root_item = $root_items->next_result())
        {
            $html[] = '<div class="category">';
            $application = $root_item->get_application();
            
            if (WebApplication :: is_application($application) && ! WebApplication :: is_active($application))
                continue;
            
            if (isset($application))
            {
                if ($application == 'root')
                    $url = 'index.php';
                else
                    $url = 'run.php?application=' . $root_item->get_application() . $root_item->get_extra();
                
                $options = '';
            }
            else
            {
                $url = $root_item->get_url();
                $options = 'target="about:blank"';
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
                    while ($subitem = $subitems->next_result())
                    {
                        $application = $subitem->get_application();
                        
                        if (WebApplication :: is_application($application) && ! WebApplication :: is_active($application))
                            continue;
                        
                        if (isset($application))
                        {
                            if ($application == 'root')
                                $url = 'index.php';
                            else
                                $url = 'run.php?application=' . $subitem->get_application() . $subitem->get_extra();
                            
                            $options = '';
                        }
                        else
                        {
                            $url = $subitem->get_url();
                            $options = 'target="about:blank"';
                        }
                        //$html_sub[] = '<li><a href="'. $url .'" ' . $options . '><img src="' . Theme :: get_image_path('admin') . 'place_mini_' . $subitem->get_application() . '.png" />'. $subitem->get_title() .'</a></li>';
                        $html_sub[] = '<div class="item"><a href="' . $url . '" ' . $options . '>' . $subitem->get_title() . '</a></div>';
                        
                        if ($this_section == $subitem->get_section())
                        {
                            $is_current = true;
                        }
                    }
                }
                
                $html[] = '<h1><a href="#" ' . ($is_current ? 'class="current"' : '') . '  ' . $options . '>' . $root_item->get_title() . '</a></h2>';
                //$html[] = '<li><a href="#" '. ($is_current ? 'class="current"' : '') .'  ' . $options . '>'. $root_item->get_title() .'</a>';
                

                $html[] = implode("\n", $html_sub);
            }
            else
            {
                $html[] = '<h1><a href="' . $url . '" ' . ($this_section == $root_item->get_section() ? 'class="current"' : '') . ' ' . $options . '>' . $root_item->get_title() . '</a></h2>';
            }
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        $user = $this->get_user();
        if (isset($user))
        {
            $html[] = '<div class="category no_margin">';
            $html[] = '<h1>' . Translation :: get('Platform') . '</h1>';
            $html[] = '<div class="item"><a href="index_user.php?go=account">' . Translation :: get('MyAccount') . '</a></div>';
            $html[] = '<div class="item"><a href="index_repository_manager.php">' . Translation :: get('Repository') . '</a></div>';
            
            if ($user->is_platform_admin())
            {
                $html[] = '<div class="item"><a href="index_admin.php">' . Translation :: get('Administration') . '</a></div>';
            }
            
            $html[] = '<div class="item"><a href="index.php?logout=true">' . Translation :: get('Logout') . '</a></div>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }
}
?>
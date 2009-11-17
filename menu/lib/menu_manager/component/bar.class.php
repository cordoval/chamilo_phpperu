<?php
/**
 * $Id: bar.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */

class MenuManagerBarComponent extends MenuManagerComponent
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
        
        $html[] = '<div class="dropnav">';
        
        while ($root_item = $root_items->next_result())
        {
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
                    $html_sub[] = '<ul>';
                    
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
                        $html_sub[] = '<li><a href="' . $url . '" ' . $options . '>' . $subitem->get_title() . '</a></li>';
                        
                        if ($this_section == $subitem->get_section())
                        {
                            $is_current = true;
                        }
                    }
                    
                    $html_sub[] = '</ul>';
                }
                
                $html[] = '<ul>';
                $html[] = '<li><a href="#" ' . ($is_current ? 'class="current"' : '') . '  ' . $options . '>' . $root_item->get_title() . '<!--[if IE 7]><!--></a><!--<![endif]-->';
                $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';
                
                $html[] = implode("\n", $html_sub);
                
                $html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
                $html[] = '</li>';
                $html[] = '</ul>';
            }
            else
            {
                $html[] = '<ul>';
                $html[] = '<li><a href="' . $url . '" ' . ($this_section == $root_item->get_section() ? 'class="current"' : '') . ' ' . $options . '>' . $root_item->get_title() . '</a></li>';
                $html[] = '</ul>';
            }
        }
        
        $user = $this->get_user();
        if (isset($user))
        {
            $html[] = '<ul class="admin">';
            $html[] = '<li class="admin"><a href="index.php?logout=true">' . Translation :: get('Logout') . '</a></li>';
            $html[] = '</ul>';
            
            if ($user->is_platform_admin())
            {
                $html[] = '<ul class="admin">';
                $html[] = '<li class="admin"><a href="index_admin.php">' . Translation :: get('Administration') . '</a></li>';
                $html[] = '</ul>';
            }
            
            $html[] = '<ul class="admin">';
            $html[] = '<li class="admin"><a href="index_repository_manager.php">' . Translation :: get('Repository') . '</a></li>';
            $html[] = '</ul>';
            
            $html[] = '<ul class="admin">';
            //$html[] = '<li class="admin"><a href="#" '. (($this_section == 'repository_manager' || $this_section == 'rights' || $this_section == 'user' || $this_section == 'platform_admin') ? 'class="current"' : '') .'>'. Translation :: get('Platform') .'<!--[if IE 7]><!--></a><!--<![endif]-->';
            //$html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';
            $html[] = '<li class="admin"><a href="index_user.php?go=account">' . Translation :: get('MyAccount') . '</a></li>';
            $html[] = '</ul>';
            
        //$html[] = '<li><a href="index_user.php?go=account">' . Translation :: get('MyAccount') . '</a></li>';
        

        //$html[] = '</ul>';
        //$html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
        //$html[] = '</li>';
        //$html[] = '</ul>';
        }
        
        //		$html[] = '<ul class="admin">';
        //		$html[] = '<li class="admin"><a href="/forum/">'.Translation :: get('Forum').'</a></li>';
        //		$html[] = '</ul>';
        

        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
?>
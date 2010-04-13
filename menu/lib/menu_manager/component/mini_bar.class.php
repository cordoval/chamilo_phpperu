<?php
/**
 * $Id: mini_bar.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */

class MenuManagerMiniBarComponent extends MenuManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();

        $root_item_condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, 0);
        $root_items = $this->retrieve_navigation_items($root_item_condition, null, null, new ObjectTableOrder(NavigationItem :: PROPERTY_SORT));

        $this_section = Header :: get_section();
        $html = array();

        $html[] = '<div class="minidropnav">';

        if (PlatformSetting::get('allow_portal_functionality') || $user->is_platform_admin())
        {
            $html[] = '<ul>';
            $html[] = '<li' . ($this_section == 'home' ? ' class="current"' : '') . '><a' . ($this_section == 'home' ? ' class="current"' : '') . ' href="index.php">' . Translation :: get('Home') . '</a></li>';
            $html[] = '</ul>';
        }

        while ($root_item = $root_items->next_result())
        {
            $application = $root_item->get_application();

            if (WebApplication :: is_application($application) && ! WebApplication :: is_active($application))
            {
                continue;
            }

            if (isset($application))
            {
                if ($application == 'root')
                {
                    $url = 'index.php';
                }
                else
                {
                    $url = 'run.php?application=' . $root_item->get_application() . $root_item->get_extra();
                }

                $options = '';
            }
            else
            {
                $url = $root_item->get_url();
                $options = 'target="about:blank"';
            }

            $subitem_condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $root_item->get_id());
            $subitems = $this->retrieve_navigation_items($subitem_condition, null, null, new ObjectTableOrder(NavigationItem :: PROPERTY_SORT));
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
                            {
                                $url = 'index.php';
                            }
                            else
                            {
                                $url = 'run.php?application=' . $subitem->get_application() . $subitem->get_extra();
                            }

                            $options = '';
                        }
                        else
                        {
                            $url = $subitem->get_url();
                            $options = 'target="about:blank"';
                        }
                        $html_sub[] = '<li><a' . ($subitems->is_last() ? ' class="last_subitem"' : '') . ' href="' . $url . '" ' . $options . '>' . $subitem->get_title() . '</a></li>';

                        if ($this_section == $subitem->get_section())
                        {
                            $is_current = true;
                        }
                    }

                    $html_sub[] = '</ul>';
                }

                $html[] = '<ul>';
                $html[] = '<li' . ($is_current ? ' class="current"' : '') . '><a href="#"' . ($is_current ? ' class="current"' : '') . '  ' . $options . '>' . $root_item->get_title() . '</a>';

                $html[] = implode("\n", $html_sub);

                $html[] = '</li>';
                $html[] = '</ul>';
            }
            else
            {
                $html[] = '<ul>';
                $html[] = '<li' . ($this_section == $root_item->get_section() ? ' class="current"' : '') . '><a href="' . $url . '" ' . ($this_section == $root_item->get_section() ? 'class="current"' : '') . ' ' . $options . '>' . $root_item->get_title() . '</a></li>';
                $html[] = '</ul>';
            }
        }

        if (isset($user))
        {
            if (PlatformSetting::get('allow_portal_functionality') || $user->is_platform_admin())
            {
                $html[] = '<ul class="admin">';
                $html[] = '<li class="admin' . ($this_section == 'my_account' ? ' current' : '') . '"><a' . ($this_section == 'my_account' ? ' class="current"' : '') . ' href="core.php?application=user&amp;go=account">' . Translation :: get('MyAccount') . '</a></li>';
                $html[] = '</ul>';

                $html[] = '<ul class="admin">';
                $html[] = '<li class="admin' . ($this_section == 'repository' ? ' current' : '') . '"><a' . ($this_section == 'repository' ? ' class="current"' : '') . ' href="core.php?application=repository">' . Translation :: get('Repository') . '</a></li>';
                $html[] = '</ul>';
            }

            if ($user->is_platform_admin())
            {
                $html[] = '<ul class="admin">';
                $html[] = '<li class="admin' . ($this_section == 'admin' ? ' current' : '') . '"><a' . ($this_section == 'admin' ? ' class="current"' : '') . ' href="core.php?application=admin">' . Translation :: get('Administration') . '</a></li>';
                $html[] = '</ul>';
            }

            $html[] = '<ul class="admin">';
            $html[] = '<li class="admin"><a href="index.php?logout=true">' . Translation :: get('Logout') . '</a></li>';
            $html[] = '</ul>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>
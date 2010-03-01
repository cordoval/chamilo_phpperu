<?php
/**
 * $Id: browser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class AdminManagerBrowserComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Administration')));
        $trail->add_help('administration');

        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $links = $this->get_application_platform_admin_links();

        $this->display_header($trail);
        echo $this->get_application_platform_admin_tabs($links);
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_application_platform_admin_tabs($links)
    {
        $html = array();
        $html[] = '<a name="top"></a>';
        $html[] = '<div id="tabs">';
        $html[] = '<ul>';

        // Render the tabs
        $index = 0;

        $selected_tab = 0;

        foreach ($links as $application_links)
        {
        	if (!count($application_links['links']))
            {
            	continue;
            }

        	$index ++;

            if(Request :: get('selected') == $application_links['application']['class'])
            {
            	$selected_tab = $index - 1;
            }

            $html[] = '<li><a href="#tabs-' . $index . '">';
            $html[] = '<span class="category">';
            $html[] = '<img src="' . Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
            $html[] = '<span class="title">' . $application_links['application']['name'] . '</span>';
            $html[] = '</span>';
            $html[] = '</a></li>';
        }

        $html[] = '</ul>';

        $index = 0;
        foreach ($links as $application_links)
        {
            if (count($application_links['links']))
            {
            	$index ++;
                $html[] = '<h2><img src="' . Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>&nbsp;' . $application_links['application']['name'] . '</h2>';
                $html[] = '<div class="tab" id="tabs-' . $index . '">';

                $html[] = '<a class="prev"></a>';

                $html[] = '<div class="items">';

                if (isset($application_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $application_links['search'], $index);

                    $html[] = '<div class="vertical_action" style="border-top: none;">';
                    $html[] = '<div class="icon">';
                    $html[] = '<img src="' . Theme :: get_image_path() . 'browse_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
                    $html[] = '</div>';
                    $html[] = $search_form->display();
                    $html[] = '</div>';
                }

                $count = 1;

                foreach ($application_links['links'] as $link)
                {
                    $count ++;

                    if ($link['confirm'])
                    {
                        $onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
                    }

                    $html[] = '<div class="vertical_action"' . ($count == 1 ? ' style="border-top: 0px solid #FAFCFC;"' : '') . '>';
                    $html[] = '<div class="icon">';
                    $html[] = '<a href="' . $link['url'] . '" ' . $onclick . '><img src="' . Theme :: get_image_path() . 'browse_' . $link['action'] . '.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/></a>';
                    $html[] = '</div>';
                    $html[] = '<div class="description">';
                    $html[] = '<h4><a href="' . $link['url'] . '" ' . $onclick . '>' . $link['name'] . '</a></h4>';
                    $html[] = $link['description'];
                    $html[] = '</div>';
                    $html[] = '</div>';
                }

                //                if (isset($application_links['search']))
                //                {
                //                    $search_form = new AdminSearchForm($this, $application_links['search'], $index);
                //
                //                    $html[] = '<div class="vertical_action">';
                //                    $html[] = '<div class="icon">';
                //                    $html[] = '<img src="' . Theme :: get_image_path() . 'browse_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
                //                    $html[] = '</div>';
                //                    $html[] = $search_form->display();
                //                    $html[] = '</div>';
                //                }


                $html[] = '</div>';

                $html[] = '<a class="next"></a>';

                $html[] = '<div class="clear"></div>';

                $html[] = '</div>';
            }
        }

        $html[] = '</div>';
        $html[] = '<br /><a href="#top">' . Translation :: get('Top') . '</a>';
        $html[] = '<script type="text/javascript">';
        $html[] = '  var tabnumber = ' . $selected_tab . ';';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/admin_ajax.js');

        return implode("\n", $html);
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_application_platform_admin_sections($links)
    {
        $html = array();
        $search_form_index = 0;
        $margin_index = 0;
        foreach ($links as $application_links)
        {
            $search_form_index ++;

            if (count($application_links['links']))
            {
                $margin_index ++;

                $html[] = '<div class="admin"' . ($margin_index % 2 == 0 ? ' style="margin-right: 0px;"' : '') . '>';
                $html[] = '<div class="admin_header">';
                $html[] = '<span class="category">';
                $html[] = '<img src="' . Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
                $html[] = '<span class="title">' . $application_links['application']['name'] . '</span>';
                $html[] = '</span>';

                if (isset($application_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $application_links['search'], $search_form_index);
                    $html[] = $search_form->display();
                }
                else
                {
                    $html[] = '<div class="admin_search">';
                    $html[] = '</div>';
                }
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';

                $html[] = '<div class="admin_section">';
                $html[] = '<div class="actions">';
                foreach ($application_links['links'] as $link)
                {
                    if ($link['confirm'])
                    {
                        $onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
                    }
                    $html[] = '<div class="action"><a href="' . $link['url'] . '" ' . $onclick . '><img src="' . Theme :: get_image_path() . 'action_' . $link['action'] . '.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/><br />' . $link['name'] . '</a></div>';
                }
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';

                $html[] = '</div>';
                $html[] = '</div>';
            }
        }

        return implode("\n", $html);
    }
}
?>
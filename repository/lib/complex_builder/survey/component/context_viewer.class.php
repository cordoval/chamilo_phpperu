<?php

require_once dirname ( __FILE__ ) . '/rel_location_browser/rel_location_browser_table.class.php';


class SurveyBuilderViewerComponent extends SurveyBuilderComponent
{
    private $category;
    private $ab;
    private $root_category;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();

        $id = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
        if ($id)
        {
            $this->category = $this->retrieve_category($id);

            $this->root_category = $this->retrieve_categories(new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, 0))->next_result();

            $category = $this->category;

            if (! $this->get_user()->is_platform_admin())
            {
                Display :: not_allowed();
            }
           
            $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseInternshipOrganizerCategories')));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $id)), $category->get_name()));
            $trail->add_help('category general');

            $this->display_header($trail);
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_category.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Name') . '</b>: ' . $category->get_name();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $category->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
            echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_users.png);">';
            echo '<div class="title">' . Translation :: get('Locations') . '</div>';
            $table = new InternshipOrganizerCategoryRelLocationBrowserTable($this, array(Application :: PARAM_ACTION => InternshipOrganizerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $id), $this->get_condition());
            echo $table->as_html();
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID));

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_CITY, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_STREET, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $locations = InternshipOrganizerDataManager::get_instance()->retrieve_locations($condition);
            while ($location = $locations->next_result())
            {
                $location_conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $location->get_id());
            }

            if (count($location_conditions))
                $conditions[] = new OrCondition($location_conditions);
            else
                $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, 0);

        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $category = $this->category;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_category_viewing_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_category_editing_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if($this->category != $this->root_category)
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_category_delete_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddLocations'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_category_suscribe_location_browser_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category->get_id());
        $locations = $this->retrieve_category_rel_locations($condition);
        $visible = ($locations->size() > 0);

        if ($visible)
        {
            $toolbar_data[] = array('href' => $this->get_category_emptying_url($category), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_category_emptying_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('TruncateNA'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

}
?>
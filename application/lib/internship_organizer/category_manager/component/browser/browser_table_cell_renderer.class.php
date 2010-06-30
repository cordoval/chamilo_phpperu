<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/category_table/default_category_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class InternshipOrganizerCategoryBrowserTableCellRenderer extends DefaultInternshipOrganizerCategoryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerCategoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $category)
    {
        if ($column === InternshipOrganizerCategoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($category);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerCategory :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $category);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_category_viewing_url($category)) . '" title="' . $title . '">' . $title_short . '</a>';
            case InternshipOrganizerCategory :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $category));
                //				if(strlen($description) > 175)
                //				{
                //					$description = mb_substr($description,0,170).'&hellip;';
                //				}
                return Utilities :: truncate_string($description);
            case Translation :: get('Locations') :
                return $category->count_locations(true, true);
            case Translation :: get('Subcategories') :
                return $category->count_children(true);
        }
        
        return parent :: render_cell($column, $category);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($category)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_category_editing_url($category), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('AddInternshipOrganizerLocation'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->browser->get_category_subscribe_location_browser_url($category), ToolbarItem :: DISPLAY_ICON));
        
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category->get_id());
        $locations = $this->browser->retrieve_category_rel_locations($condition);
        $visible = ($locations->size() > 0);
        
        if ($visible)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->browser->get_category_emptying_url($category), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_category_delete_url($category), ToolbarItem :: DISPLAY_ICON, true));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->browser->get_move_category_url($category), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>
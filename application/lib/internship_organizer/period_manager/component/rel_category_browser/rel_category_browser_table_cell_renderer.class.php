<?php

require_once dirname(__FILE__) . '/rel_category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/category_rel_period_table/default_category_rel_period_table_cell_renderer.class.php';

class InternshipOrganizerCategoryRelPeriodBrowserTableCellRenderer extends DefaultInternshipOrganizerCategoryRelPeriodTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerCategoryRelPeriodBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $category_rel_period)
    {
        if ($column === InternshipOrganizerCategoryRelPeriodBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($category_rel_period);
        }
        
        return parent :: render_cell($column, $category_rel_period);
    }

    private function get_modification_links($category_rel_period)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_period_unsubscribe_category_url($category_rel_period), ToolbarItem :: DISPLAY_ICON, true));
        return $toolbar->as_html();
    }
}
?>
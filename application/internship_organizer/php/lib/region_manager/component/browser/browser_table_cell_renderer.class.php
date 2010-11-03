<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/region_table/default_region_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class InternshipOrganizerRegionBrowserTableCellRenderer extends DefaultInternshipOrganizerRegionTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerRegionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $region)
    {
        if ($column === InternshipOrganizerRegionBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($column, $region);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                $title = parent :: render_cell($column, $region);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_browse_regions_url($region)) . '" title="' . $title . '">' . $title_short . '</a>';
            case InternshipOrganizerRegion :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $region));
                return Utilities :: truncate_string($description);
            case Translation :: get('Subregions') :
                return $region->count_children(true);
        }
        
        return parent :: render_cell($column, $region);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($column, $region)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_region_editing_url($region), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_region_delete_url($region), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>
<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/period_table/default_period_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class InternshipOrganizerPeriodBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerPeriodBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $period)
    {
        if ($column === InternshipOrganizerPeriodBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($period);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerPeriod :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $period);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_period_viewing_url($period)) . '" title="' . $title . '">' . $title_short . '</a>';
            case InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $period));
                return Utilities :: truncate_string($description);
            case Translation :: get('Subperiods') :
                return $period->count_children(true);
        }
        
        return parent :: render_cell($column, $period);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($period)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_period_editing_url($period), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
        $toolbar_data[] = array('href' => $this->browser->get_period_delete_url($period), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
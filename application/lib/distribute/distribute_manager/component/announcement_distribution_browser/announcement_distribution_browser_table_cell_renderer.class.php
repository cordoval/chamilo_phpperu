<?php
/**
 * $Id: announcement_distribution_browser_table_cell_renderer.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.distribute_manager.component.announcement_distribution_browser
 */
require_once dirname(__FILE__) . '/announcement_distribution_browser_table_column_model.class.php';
require_once Path :: get_application_path() . 'lib/distribute/tables/announcement_distribution_table/default_announcement_distribution_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../distribute_manager.class.php';
/**
 * Cell renderer for the announcement distribution browser table
 */
class AnnouncementDistributionBrowserTableCellRenderer extends DefaultAnnouncementDistributionTableCellRenderer
{
    /**
     * The distribute browser component
     */
    private $browser;

    /**
     * Constructor
     * @param PersonalMessengerManagerBrowserComponent $browser
     */
    function AnnouncementDistributionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $announcement_distribution)
    {
        if ($column === AnnouncementDistributionBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($announcement_distribution);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case AnnouncementDistribution :: PROPERTY_PUBLISHED :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $announcement_distribution->get_published());
                break;
            case AnnouncementDistribution :: PROPERTY_STATUS :
                return $announcement_distribution->get_status_icon();
            case AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT :
                $title = parent :: render_cell($column, $announcement_distribution);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($this->browser->get_announcement_distribution_viewing_url($announcement_distribution)) . '" title="' . $title . '">' . $title_short . '</a>';
                break;
        }
        return parent :: render_cell($column, $announcement_distribution);
    }

    /**
     * Gets the action links to display
     * @param AnnouncementDistribution $announcement_distribution The announcement distribution for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($announcement_distribution)
    {
        $toolbar_data = array();
        
        //		$delete_url = $this->browser->get_publication_deleting_url($announcement_distribution);
        //		$toolbar_data[] = array(
        //			'href' => $delete_url,
        //			'label' => Translation :: get('Delete'),
        //			'confirm' => true,
        //			'img' => Theme :: get_common_image_path().'action_delete.png'
        //		);
        

        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
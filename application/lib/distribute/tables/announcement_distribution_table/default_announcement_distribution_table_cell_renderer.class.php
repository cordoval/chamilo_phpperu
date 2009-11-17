<?php
/**
 * $Id: default_announcement_distribution_table_cell_renderer.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.tables.announcement_distribution_table
 */

require_once Path :: get_application_path() . 'lib/distribute/announcement_distribution.class.php';

class DefaultAnnouncementDistributionTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultAnnouncementDistributionTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $announcement_distribution)
    {
        switch ($column->get_name())
        {
            case AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT :
                return $announcement_distribution->get_distribution_object()->get_title();
            case AnnouncementDistribution :: PROPERTY_PUBLISHER :
                $user = $announcement_distribution->get_distribution_publisher();
                if ($user)
                {
                    return $user->get_fullname();
                }
                else
                {
                    return Translation :: get('DistributorUnknown');
                }
            case AnnouncementDistribution :: PROPERTY_PUBLISHED :
                return $announcement_distribution->get_published();
            case AnnouncementDistribution :: PROPERTY_STATUS :
                return $announcement_distribution->get_status();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
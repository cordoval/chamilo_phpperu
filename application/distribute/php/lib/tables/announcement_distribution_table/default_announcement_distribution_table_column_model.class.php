<?php
/**
 * $Id: default_announcement_distribution_table_column_model.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.tables.announcement_distribution_table
 */
require_once Path :: get_application_path() . 'lib/distribute/announcement_distribution.class.php';

class DefaultAnnouncementDistributionTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultAnnouncementDistributionTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_STATUS);
        $columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT);
        $columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_PUBLISHER);
        $columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_PUBLISHED);
        return $columns;
    }
}
?>
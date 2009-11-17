<?php
/**
 * $Id: default_system_announcement_publication_table_column_model.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.system_announcement_publication_table
 */

class DefaultSystemAnnouncementPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSystemAnnouncementPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ProfileTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(SystemAnnouncementPublication :: PROPERTY_CONTENT_OBJECT_ID);
        $columns[] = new ObjectTableColumn(SystemAnnouncementPublication :: PROPERTY_PUBLISHED);
        return $columns;
    }
}
?>
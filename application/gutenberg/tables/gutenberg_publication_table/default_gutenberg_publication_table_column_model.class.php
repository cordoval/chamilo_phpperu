<?php
/**
 * $Id: default_gutenberg_publication_table_column_model.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg.tables.gutenberg_publication_table
 */
require_once dirname(__FILE__) . '/../../gutenberg_publication.class.php';

class DefaultGutenbergPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultGutenbergPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return GutenbergTableColumn[]
     */
    private static function get_default_columns()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $content_object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
        return $columns;
    }
}
?>
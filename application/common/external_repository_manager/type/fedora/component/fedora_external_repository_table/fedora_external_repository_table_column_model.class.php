<?php

require_once Path::get_common_path() . '/html/table/object_table/object_table_column_model.class.php';

/**
 * Table column model for the repository browser table
 */
class FedoraExternalRepositoryTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct(self::get_default_columns(), 2);
        //$this->set_default_order_column(1);
        $this->add_column(self::get_modification_column());
    }


    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
       // $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_TITLE, true);
       // $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_CREATED, true);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_MODIFIED, true);
        $columns[] = new ObjectTableColumn(ExternalRepositoryObject::PROPERTY_DESCRIPTION, false);
        return $columns;
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self::$modification_column))
        {
            self::$modification_column = new StaticTableColumn('');
        }
        return self::$modification_column;
    }
}
?>
<?php




class DefaultHandbookPublicationTableColumnModel extends ObjectTableColumnModel
{

  
    function DefaultHandbookPublicationTableColumnModel()
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
        $columns[] = new StaticTableColumn('', false);
        $columns[] = new ObjectTableColumn(Handbook::PROPERTY_TITLE, true);
        $columns[] = new ObjectTableColumn(Handbook::PROPERTY_DESCRIPTION, true);
       
      
       
        



        return $columns;
    }
}
?>
<?php
namespace application\portfolio;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use user\User;




class DefaultPortfolioTableColumnModel extends ObjectTableColumnModel
{

  
    function __construct()
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
//        $columns[] = new StaticTableColumn('', false);
        $columns[] = new ObjectTableColumn(User::PROPERTY_FIRSTNAME, true);
        $columns[] = new ObjectTableColumn(User::PROPERTY_LASTNAME, true);
        $columns[] = new ObjectTableColumn(User::PROPERTY_OFFICIAL_CODE, true);
      
       
        



        return $columns;
    }
}
?>
<?php 
namespace application\survey;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use user\User;

class DefaultSurveyUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return TrainingTrackTableColumn[]
     */
    private static function get_default_columns()
    {
        
        $columns = array();
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true);
        return $columns;
    
    }
}
?>
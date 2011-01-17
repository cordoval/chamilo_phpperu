<?php 
namespace repository\content_object\survey;

use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableFormActions;

class SurveyUserTableColumnModel extends DefaultSurveyUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($component)
    {
        parent::__construct($component);
    	$this->add_column(self :: get_modification_column());
    }

    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>

<?php
namespace application\weblcms\tool\learning_path;

use common\libraries\ObjectTableColumn;
use application\weblcms\ObjectPublicationTableColumnModel;

/**
 * $Id: learning_path_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_browser
 */
/**
 * This class is a cell renderer for a publication candidate table
 */
class LearningPathColumnModel extends ObjectPublicationTableColumnModel
{

    function __construct()
    {
        parent :: __construct($this->get_columns());
    }

    function get_columns()
    {
        $columns = parent :: get_basic_columns();
        $columns[] = new ObjectTableColumn('progress', false);
        $columns[] = parent :: get_action_column();
        return $columns;
    }
}
?>
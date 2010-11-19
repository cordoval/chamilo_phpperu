<?php
namespace application\weblcms\tool\assessment;

use common\libraries\ObjectTableColumn;
use application\weblcms\ObjectPublicationTableColumnModel;
use repository\content_object\assessment\Assessment;

/**
 * $Id: assessment_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_browser
 */
require_once dirname(__FILE__) . '/../../../../../php/lib/browser/object_publication_table/object_publication_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentColumnModel extends ObjectPublicationTableColumnModel
{

    function __construct()
    {
        parent :: __construct($this->get_columns());
    }

    function get_columns()
    {
        $columns = parent :: get_basic_columns();
        $columns[] = new ObjectTableColumn(Assessment :: PROPERTY_ASSESSMENT_TYPE, false);
        $columns[] = parent :: get_action_column();
        return $columns;
    }
}
?>
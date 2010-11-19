<?php
namespace repository\content_object\assessment;

use common\libraries\Translation;
use repository\ComplexBrowserTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * $Id: assessment_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class AssessmentBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{

    /**
     * Constructor
     */
    function __construct($browser)
    {
        $columns[] = new ObjectTableColumn('weight', false);
        parent :: __construct($browser, $columns);
    }
}
?>
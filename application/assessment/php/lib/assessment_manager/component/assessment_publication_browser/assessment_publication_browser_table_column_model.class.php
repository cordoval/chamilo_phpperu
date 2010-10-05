<?php
/**
 * $Id: assessment_publication_browser_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_publication_browser
 */

require_once dirname(__FILE__) . '/../../../tables/assessment_publication_table/default_assessment_publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../assessment_publication.class.php';

/**
 * Table column model for the assessment_publication browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class AssessmentPublicationBrowserTableColumnModel extends DefaultAssessmentPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AssessmentPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
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
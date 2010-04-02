<?php
/**
 * Table column model for the repository browser table
 */
class PeerAssessmentBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{

    /**
     * Constructor
     */
    function PeerAssessmentBrowserTableColumnModel($show_subitems_column)
    {
        $columns[] = new StaticTableColumn(Translation :: get('AddDate'));
        parent :: __construct($show_subitems_column, $columns);
    }
}
?>

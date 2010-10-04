<?php

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_data_provider.class.php';

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_cell_renderer.class.php';

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_column_model.class.php';

/**
 * Table to display the content object share rights.
 * @author Pieterjan Broekaert
 */
class ContentObjectGroupShareRightsBrowserTable extends ObjectTable
{

    function ContentObjectGroupShareRightsBrowserTable($browser, $parameters, $condition)
    {
        $model = new ContentObjectGroupShareRightsBrowserTableColumnModel();
        $renderer = new ContentObjectGroupShareRightsBrowserTableCellRenderer($browser);
        $data_provider = new ContentObjectGroupShareRightsBrowserTableDataProvider($browser, $condition);
        ObjectTable :: __construct($data_provider, Utilities :: underscores_to_camelcase(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

}

?>
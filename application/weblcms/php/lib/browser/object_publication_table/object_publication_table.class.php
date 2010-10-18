<?php
/**
 * $Id: object_publication_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.object_publication_table
 */
require_once dirname(__FILE__) . '/object_publication_table_data_provider.class.php';
require_once dirname(__FILE__) . '/object_publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/object_publication_table_cell_renderer.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class ObjectPublicationTable extends ObjectTable
{
    function ObjectPublicationTable($table_renderer, $condition, $cell_renderer = null, $column_model = null)
    {
        $data_provider = new ObjectPublicationTableDataProvider($table_renderer, $condition);
        
        if (! $column_model)
        {
            $column_model = new ObjectPublicationTableColumnModel();
        }
        
        if (! $cell_renderer)
        {
            $cell_renderer = new ObjectPublicationTableCellRenderer($table_renderer);
        }
        
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $column_model, $cell_renderer);
        
        $cell_renderer->set_object_count($this->get_object_count());
        $actions = $table_renderer->get_actions();
        
        $this->set_form_actions($actions);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(Tool :: PARAM_PUBLICATION_ID, $ids);
    }
}
?>
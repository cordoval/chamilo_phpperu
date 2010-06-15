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
    const DEFAULT_NAME = 'publication_table';

    function ObjectPublicationTable($parent, $owner, $types, $condition, $cell_renderer = null, $column_model = null)
    {
        $data_provider = new ObjectPublicationTableDataProvider($parent, $owner, $types, $condition);

        if (! $column_model)
        {
            $column_model = new ObjectPublicationTableColumnModel();
        }

        if (! $cell_renderer)
        {
            $cell_renderer = new ObjectPublicationTableCellRenderer($parent);
        }

        parent :: __construct($data_provider, ObjectPublicationTable :: DEFAULT_NAME, $column_model, $cell_renderer);

        $cell_renderer->set_object_count($this->get_object_count());
        $actions = array();
        if($parent->is_allowed(DELETE_RIGHT))
        {
        	$actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('RemoveSelected'));
        }
		if($parent->is_allowed(EDIT_RIGHT))
		{
        	$actions[] = new ObjectTableFormAction(Tool :: ACTION_MOVE_SELECTED_TO_CATEGORY, Translation :: get('MoveSelected'), false);
		}

        $this->set_form_actions($actions);
    }
}
?>
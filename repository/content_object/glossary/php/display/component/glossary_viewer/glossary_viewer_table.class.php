<?php
namespace repository\content_object\glossary;

use common\libraries\ObjectTable;

/**
 * $Id: glossary_viewer_table.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary.component.glossary_viewer
 */
require_once dirname(__FILE__) . '/glossary_viewer_table_data_provider.class.php';
require_once dirname(__FILE__) . '/glossary_viewer_table_column_model.class.php';
require_once dirname(__FILE__) . '/glossary_viewer_table_cell_renderer.class.php';
//require_once dirname(__FILE__).'/../../../../content_object_results_table.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * results.
 */
class GlossaryViewerTable extends ObjectTable
{
    const DEFAULT_NAME = 'glossary_viewer_table_';

    /**
     * Constructor.
     * @param int $owner The id of the current user.
     * @param array $types The types of objects that can be published in current
     * location.
     * @param string $query The search query, or null if none.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     * @see ResultsCandidateTableCellRenderer::ResultsCandidateTableCellRenderer()
     */
    function GlossaryViewerTable($parent)
    {
        $data_provider = new GlossaryViewerTableDataProvider($parent);
        $column_model = new GlossaryViewerTableColumnModel();
        $cell_renderer = new GlossaryViewerTableCellRenderer($parent);
        parent :: __construct($data_provider, GlossaryViewerTable :: DEFAULT_NAME, $column_model, $cell_renderer);
    }
}
?>
<?php
/**
 * $Id: phrases_mastery_level_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_mastery_level_browser
 */
require_once dirname(__FILE__) . '/phrases_mastery_level_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/phrases_mastery_level_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/phrases_mastery_level_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../phrases_manager.class.php';

/**
 * Table to display a list of phrases_mastery_levels
 *
 * @author Hans De Bisschop
 * @author
 */
class PhrasesMasteryLevelBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'phrases_mastery_level_browser_table';

    /**
     * Constructor
     */
    function PhrasesMasteryLevelBrowserTable($browser, $parameters, $condition)
    {
        $model = new PhrasesMasteryLevelBrowserTableColumnModel();
        $renderer = new PhrasesMasteryLevelBrowserTableCellRenderer($browser);
        $data_provider = new PhrasesMasteryLevelBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $renderer->set_object_count($this->get_object_count());

        $actions = array();

//        $actions[] = new ObjectTableFormAction(PhrasesManager :: PARAM_DELETE_SELECTED_ASSESSMENT_MASTERY_LEVELS, Translation :: get('RemoveSelected'));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>
<?php
/**
 * $Id: phrases_publication_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_publication_browser
 */
require_once dirname(__FILE__) . '/phrases_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/phrases_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/phrases_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../phrases_manager.class.php';

/**
 * Table to display a list of phrases_publications
 *
 * @author Hans De Bisschop
 * @author 
 */
class PhrasesPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'phrases_publication_browser_table';

    /**
     * Constructor
     */
    function PhrasesPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new PhrasesPublicationBrowserTableColumnModel();
        $renderer = new PhrasesPublicationBrowserTableCellRenderer($browser);
        $data_provider = new PhrasesPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
//        $actions[] = new ObjectTableFormAction(PhrasesManager :: PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>
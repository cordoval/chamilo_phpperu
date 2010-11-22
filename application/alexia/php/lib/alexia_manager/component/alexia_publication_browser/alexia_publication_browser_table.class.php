<?php

namespace application\alexia;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * $Id: alexia_publication_browser_table.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once WebApplication :: get_application_class_lib_path('alexia') . 'alexia_manager/component/alexia_publication_browser/alexia_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('alexia') . 'alexia_manager/component/alexia_publication_browser/alexia_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('alexia') . 'alexia_manager/component/alexia_publication_browser/alexia_publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class AlexiaPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'alexia_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $name, $parameters, $condition)
    {
        $model = new AlexiaPublicationBrowserTableColumnModel();
        $renderer = new AlexiaPublicationBrowserTableCellRenderer($browser);
        $data_provider = new AlexiaPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, AlexiaPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(AlexiaManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected', null, Utilities::COMMON_LIBRARIES));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>
<?php
namespace repository;

use common\libraries\ComplexMenuSupport;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: complex_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.complex_browser
 */
require_once dirname(__FILE__) . '/complex_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/complex_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/complex_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ComplexBrowserTable extends ObjectTable
{

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition, $show_subitems_column = true, $model = null, $renderer = null, $name = null)
    {
        $name = Utilities :: get_classname_from_namespace(_CLASS__, true);

        if (! $model)
        {
            $model = new ComplexBrowserTableColumnModel($browser);
        }

        if (! $renderer)
        {
            $renderer = new ComplexBrowserTableCellRenderer($browser, $condition);
        }

        $data_provider = new ComplexBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, $name, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__, ComplexBuilder :: PARAM_BUILDER_ACTION);

        $action = ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM;
        //        if ($name != self :: DEFAULT_NAME)
        //            $action = ComplexBuilder :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_' . $name;


        $actions->add_form_action(new ObjectTableFormAction($action, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

        if ($browser instanceof ComplexMenuSupport)
        {
            $action = ComplexBuilder :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM;
            //            if ($name != self :: DEFAULT_NAME)
            //                $action = ComplexBuilder :: PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_' . $name;


            $actions->add_form_action(new ObjectTableFormAction($action, Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES), false));
        }

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $ids);
    }
}
?>
<?php
namespace repository;

use common\libraries\ObjectTable;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\SubManager;
use Exception;

/**
 * @package repository.lib.complex_display
 *
 * @author Hans De Bisschop
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_builder/complex_menu.class.php';

abstract class ComplexDisplay extends SubManager
{
    const PARAM_DISPLAY_ACTION = 'display_action';
    const PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'cloi';
    const PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'selected_cloi';
    const PARAM_DIRECTION = 'direction';
    const PARAM_TYPE = 'type';

    const ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM = 'deleter';
    const ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM = 'updater';
    const ACTION_UPDATE_CONTENT_OBJECT = 'content_object_updater';
    const ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM = 'creator';
    const ACTION_VIEW_ATTACHMENT = 'attachment_viewer';
    const ACTION_VIEW_COMPLEX_CONTENT_OBJECT = 'viewer';
    const ACTION_CREATE_FEEDBACK = 'complex_feedback';
    const ACTION_EDIT_FEEDBACK = 'feedback_editor';
    const ACTION_DELETE_FEEDBACK = 'feedback_deleter';

    const DEFAULT_ACTION = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    protected $menu;

    /**
     * The current item in treemenu to determine where we are in the structure
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     * The item we select to execute an action like update / delete / move etc
     * @var ComplexContentObjectItem
     */
    private $selected_complex_content_object_item;

    function __construct($parent)
    {
        parent :: __construct($parent);

        $action = Request :: get(self :: PARAM_DISPLAY_ACTION);
        $this->set_action($action);

        $this->set_parameter(self :: PARAM_TYPE, Request :: get(self :: PARAM_TYPE));

        $complex_content_object_item_id = Request :: get(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $this->complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
        }

        $selected_complex_content_object_item_id = Request :: get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($selected_complex_content_object_item_id && ! is_array($selected_complex_content_object_item_id))
        {
            $this->selected_complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($selected_complex_content_object_item_id);
        }

     //$this->parse_input_from_table();
    }

    static function factory($parent, $type)
    {
        $file = dirname(__FILE__) . '/../../../content_object/' . $type . '/php/display/' . $type . '_display.class.php';
        require_once $file;
        $class = Utilities :: underscores_to_camelcase($type) . 'Display';
        return new $class($parent);
    }

    /**
     * @param string $type
     * @param Application $application
     */
    static function launch($type, $application, $add_breadcrumb = true)
    {
        $file = dirname(__FILE__) . '/../../../content_object/' . $type . '/php/display/' . $type . '_display.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ComplexDisplayTypeDoesNotExist', array('TYPE' => $type)));
        }

        require_once $file;

        $class = 'repository\\content_object\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'Display';

        parent :: launch($class, $application, $add_breadcrumb);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_DISPLAY_ACTION);
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_DISPLAY_ACTION, $action);
    }

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    /**
     * @param ComplexContentObjectItem $complex_content_object_item
     */
    function set_complex_content_object_item(ComplexContentObjectItem $complex_content_object_item)
    {
        $this->complex_content_object_item = $complex_content_object_item;
    }

    function get_selected_complex_content_object_item()
    {
        return $this->selected_complex_content_object_item;
    }

    function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object()->get_id();
    }

    function get_complex_content_object_item_id()
    {
        if ($this->complex_content_object_item)
        {
            return $this->complex_content_object_item->get_id();
        }
    }

    function get_selected_complex_content_object_item_id()
    {
        if ($this->selected_complex_content_object_item)
        {
            return $this->selected_complex_content_object_item->get_id();
        }
    }

    // Common Code
    function get_complex_content_object_menu()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->render_as_tree();
    }

    function get_complex_content_object_breadcrumbs()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->get_breadcrumbs();
    }

    protected function build_complex_content_object_menu()
    {
        $this->menu = new ComplexMenu($this->get_root_content_object(), $this->get_complex_content_object_item(), $this->get_url(array(
                self :: PARAM_DISPLAY_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT)));
    }

    //url building


    function get_complex_content_object_item_update_url($complex_content_object_item)
    {
        return $this->get_url(array(
                self :: PARAM_DISPLAY_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    function get_complex_content_object_item_delete_url($complex_content_object_item)
    {
        return $this->get_url(array(
                self :: PARAM_DISPLAY_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(),
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    protected function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[ObjectTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                    $this->set_action(self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM);
                    Request :: set_get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $selected_ids);
                    break;
            }
        }
    }
}

?>
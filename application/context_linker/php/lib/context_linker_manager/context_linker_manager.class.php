<?php
namespace application\context_linker;

use common\libraries\ObjectTable;
use common\libraries\WebApplication;
use common\libraries\ArrayResultSet;

/**
 * A context_linker manager
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManager extends WebApplication
{
    const APPLICATION_NAME = 'context_linker';

    const ARRAY_TYPE_FLAT = '1';
    const ARRAY_TYPE_RECURSIVE = '2';

    const PARAM_VIEW = 'view';
    const VIEW_TABLE = 'table';
    const VIEW_GRAPHIC = 'graphic';

    const RECURSIVE_DIRECTION_UP = '1';
    const RECURSIVE_DIRECTION_DOWN = '2';
    const RECURSIVE_DIRECTION_BOTH = '3';

    const ACTION_BROWSE_CONTENT_OBJECTS = 'content_objects_browser';

    const PARAM_CONTEXT_LINK = 'context_link';
    const PARAM_DELETE_SELECTED_CONTEXT_LINKS = 'delete_selected_context_links';

    const ACTION_DELETE_CONTEXT_LINK = 'context_link_deleter';
    const ACTION_EDIT_CONTEXT_LINK = 'context_link_updater';
    const ACTION_CREATE_CONTEXT_LINK = 'context_link_creator';
    const ACTION_PUBLISH_CONTEXT_LINK = 'context_link_publisher';
    const ACTION_BROWSE_CONTEXT_LINKS = 'context_links_browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_CONTENT_OBJECTS;

    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_ALTERNATIVE_CONTENT_OBJECT_ID = 'alternative_content_object_id';
    const PARAM_PROPERTY_VALUE = 'property_value';

    const PROPERTY_ALT_ID = 'alt_id';
    const PROPERTY_ALT_TYPE = 'alt_type';
    const PROPERTY_ALT_TITLE = 'alt_title';
    const PROPERTY_ORIG_ID = 'orig_id';
    const PROPERTY_ORIG_TYPE = 'orig_type';
    const PROPERTY_ORIG_TITLE = 'orig_title';

    /**
     * Constructor
     * @param User $user The current user
     */
    function __construct($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_CONTEXT_LINKS :

                    $selected_ids = $_POST[ContextLinkBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_CONTEXT_LINK);
                    $_GET[self :: PARAM_CONTEXT_LINK] = $selected_ids;
                    break;
            }
        }
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function get_default_action()
    {
        return self :: DEFAULT_ACTION;

    }

    // Data Retrieving


    function count_context_links($condition)
    {
        return ContextLinkerDataManager :: get_instance()->count_context_links($condition);
    }

    function retrieve_context_links($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return ContextLinkerDataManager :: get_instance()->retrieve_context_links($condition, $offset, $count, $order_property);
    }

    function retrieve_full_context_links($condition = null, $offset = null, $count = null, $order_property = null, $array_type = self :: ARRAY_TYPE_FLAT)
    {
        //return ContextLinkerDataManager :: get_instance()->retrieve_full_context_links_recursive($condition, $offset, $count, $order_property, array(), $array_type);
        return new ArrayResultSet(ContextLinkerDataManager :: get_instance()->retrieve_full_context_links_recursive($condition, $offset, $count, $order_property, $array_type));
    }

    function retrieve_context_link($id)
    {
        return ContextLinkerDataManager :: get_instance()->retrieve_context_link($id);
    }

    // Url Creation


    function get_create_context_link_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_LINK));
    }

    function get_update_context_link_url($context_link)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_LINK,
                self :: PARAM_CONTEXT_LINK => $context_link[ContextLink :: PROPERTY_ID]));
    }

    function get_delete_context_link_url($context_link)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_LINK,
                self :: PARAM_CONTEXT_LINK => $context_link[ContextLink :: PROPERTY_ID]));
    }

    function get_browse_context_links_url($content_object)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_LINKS,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}
?>
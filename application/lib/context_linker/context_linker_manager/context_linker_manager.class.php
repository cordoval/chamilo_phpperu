<?php
/**
 * @package application.lib.context_linker.context_linker_manager
 */
require_once dirname(__FILE__).'/../context_linker_data_manager.class.php';
require_once dirname(__FILE__).'/component/context_link_browser/context_link_browser_table.class.php';

/**
 * A context_linker manager
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
 class ContextLinkerManager extends WebApplication
 {
    const APPLICATION_NAME = 'context_linker';

    const PARAM_CONTEXT_LINK = 'context_link';
    const PARAM_DELETE_SELECTED_CONTEXT_LINKS = 'delete_selected_context_links';

    const ACTION_DELETE_CONTEXT_LINK = 'context_link_deleter';
    const ACTION_EDIT_CONTEXT_LINK = 'context_link_updater';
    const ACTION_CREATE_CONTEXT_LINK = 'context_link_creator';
    const ACTION_PUBLISH_CONTEXT_LINK = 'context_link_publisher';
    const ACTION_BROWSE_CONTEXT_LINKS = 'context_links_browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_CONTEXT_LINKS;

    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_ALTERNATIVE_CONTENT_OBJECT_ID = 'alternative_content_object_id';
    const PARAM_PROPERTY_VALUE = 'property_value';
    
    /**
     * Constructor
     * @param User $user The current user
     */
    function ContextLinkerManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    private function parse_input_from_table()
    {
        if (isset ($_POST['action']))
        {
            switch ($_POST['action'])
            {
                    case self :: PARAM_DELETE_SELECTED_CONTEXT_LINKS :

                            $selected_ids = $_POST[ContextLinkBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

                            if (empty ($selected_ids))
                            {
                                    $selected_ids = array ();
                            }
                            elseif (!is_array($selected_ids))
                            {
                                    $selected_ids = array ($selected_ids);
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

    function get_default_action() {
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

    function retrieve_full_context_links($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return ContextLinkerDataManager :: get_instance()->retrieve_full_context_links($condition, $offset, $count, $order_property);
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
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_LINK,
                                                                self :: PARAM_CONTEXT_LINK => $context_link[ContextLink:: PROPERTY_ID]));
    }

    function get_delete_context_link_url($context_link)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_LINK,
                                                                self :: PARAM_CONTEXT_LINK => $context_link[ContextLink:: PROPERTY_ID]));
    }

    function get_browse_context_links_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_LINKS));
    }

    function get_browse_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}
?>
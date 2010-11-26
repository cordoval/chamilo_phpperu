<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use rights\RightsManager;

/**
 * $Id: external_instance_instance_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/external_instance_instance_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../external_instance_instance_table/default_external_instance_instance_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ExternalInstanceInstanceBrowserTableCellRenderer extends DefaultExternalInstanceInstanceTableCellRenderer
{
    private $browser;

    /**
     * Constructor
     * @param ExternalRepositoryInstanceManager $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $external_instance)
    {
        if ($column === ExternalInstanceInstanceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($external_instance);
        }

//        switch ($column->get_name())
//        {
//            //            case ContentObject :: PROPERTY_TYPE :
//        //                return '<a href="' . htmlentities($this->browser->get_type_filter_url($external_repository->get_type())) . '">' . parent :: render_cell($column, $external_repository) . '</a>';
//        //            case ContentObject :: PROPERTY_TITLE :
//        //                $title = parent :: render_cell($column, $external_repository);
//        //                $title_short = Utilities :: truncate_string($title, 53, false);
//        //                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($external_repository)) . '" title="' . $title . '">' . $title_short . '</a>';
//        }
        return parent :: render_cell($column, $external_instance);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($external_instance)
    {
        $toolbar = new Toolbar();

        if ($external_instance->is_enabled())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Deactivate', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_deactivate.png', $this->browser->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_DEACTIVATE_INSTANCE, ExternalInstanceInstanceManager :: PARAM_INSTANCE => $external_instance->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Activate', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_activate.png', $this->browser->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_ACTIVATE_INSTANCE, ExternalInstanceInstanceManager :: PARAM_INSTANCE => $external_instance->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        }

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_UPDATE_INSTANCE, ExternalInstanceInstanceManager :: PARAM_INSTANCE => $external_instance->get_id())), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_DELETE_INSTANCE, ExternalInstanceInstanceManager :: PARAM_INSTANCE => $external_instance->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights', null, RightsManager :: APPLICATION_NAME), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_MANAGE_INSTANCE_RIGHTS, ExternalInstanceInstanceManager :: PARAM_INSTANCE => $external_instance->get_id())), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
?>
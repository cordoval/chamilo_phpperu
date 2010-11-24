<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use rights\RightsManager;

/**
 * $Id: video_conferencing_instance_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/video_conferencing_instance_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../video_conferencing_instance_table/default_video_conferencing_instance_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class VideoConferencingInstanceBrowserTableCellRenderer extends DefaultVideoConferencingInstanceTableCellRenderer
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
    function render_cell($column, $video_conferencing)
    {
        if ($column === VideoConferencingInstanceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($video_conferencing);
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
        return parent :: render_cell($column, $video_conferencing);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($external_repository)
    {
        $toolbar = new Toolbar();

        if ($external_repository->is_enabled())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Deactivate', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_deactivate.png', $this->browser->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_DEACTIVATE_INSTANCE, VideoConferencingInstanceManager :: PARAM_INSTANCE => $video_conferencing->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Activate', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_activate.png', $this->browser->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_ACTIVATE_INSTANCE, VideoConferencingInstanceManager :: PARAM_INSTANCE => $video_conferencing->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        }

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_UPDATE_INSTANCE, VideoConferencingInstanceManager :: PARAM_INSTANCE => $video_conferencing->get_id())), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_DELETE_INSTANCE, VideoConferencingInstanceManager :: PARAM_INSTANCE => $video_conferencing->get_id())), ToolbarItem :: DISPLAY_ICON, true));
        $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights', null, RightsManager :: APPLICATION_NAME), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_MANAGE_INSTANCE_RIGHTS, VideoConferencingInstanceManager :: PARAM_INSTANCE => $video_conferencing->get_id())), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
?>
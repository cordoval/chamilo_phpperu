<?php
namespace admin;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\Utilities;
/**
 * $Id: video_conferencing_manager_registration_browser_table.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/video_conferencing_manager/video_conferencing_manager_registration_browser_table_data_provider.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/video_conferencing_manager/video_conferencing_manager_registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/video_conferencing_manager/video_conferencing_manager_registration_browser_table_cell_renderer.class.php';

/**
 * Table to display a set of learning objects.
 */
class VideoConferencingManagerRegistrationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'video_conferencing_manager_registration_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new VideoConferencingManagerRegistrationBrowserTableColumnModel();
        $renderer = new VideoConferencingManagerRegistrationBrowserTableCellRenderer($browser);
        $data_provider = new VideoConferencingManagerRegistrationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, VideoConferencingManagerRegistrationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

        $actions[] = new ObjectTableFormAction(PackageManager :: PARAM_ACTIVATE_SELECTED, Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES), false);
        $actions[] = new ObjectTableFormAction(PackageManager :: PARAM_DEACTIVATE_SELECTED, Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES), false);

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>
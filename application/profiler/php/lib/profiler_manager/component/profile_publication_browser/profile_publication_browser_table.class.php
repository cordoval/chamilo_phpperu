<?php

namespace application\profiler;

use common\libraries\WebApplication;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
/**
 * $Id: profile_publication_browser_table.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component.profile_publication_browser
 */
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_manager/component/profile_publication_browser/profile_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_manager/component/profile_publication_browser/profile_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_manager/component/profile_publication_browser/profile_publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ProfilePublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'profile_publication_browser_table';

    /**
     * Constructor
     */
    function ProfilePublicationBrowserTable($browser, $name, $parameters, $condition)
    {
        $model = new ProfilePublicationBrowserTableColumnModel();
        $renderer = new ProfilePublicationBrowserTableCellRenderer($browser);
        $data_provider = new ProfilePublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, ProfilePublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(ProfilerManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected', null , Utilities :: COMMON_LIBRARIES));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>
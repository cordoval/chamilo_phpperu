<?php

namespace application\personal_messenger;

use common\libraries\WebApplication;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\ObjectTable;
/**
 * $Id: pm_publication_browser_table.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component.pm_publication_browser
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of pm publications.
 */
class PmPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'pm_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $name, $parameters, $condition)
    {
        $folder = $browser->get_folder();
        $model = new PmPublicationBrowserTableColumnModel($folder);
        $renderer = new PmPublicationBrowserTableCellRenderer($browser);
        $data_provider = new PmPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, PmPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(PersonalMessengerManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($folder == PersonalMessengerManager :: FOLDER_INBOX)
        {
            $actions[] = new ObjectTableFormAction(PersonalMessengerManager :: PARAM_MARK_SELECTED_READ, Translation :: get('MarkSelectedRead'), false);
            $actions[] = new ObjectTableFormAction(PersonalMessengerManager :: PARAM_MARK_SELECTED_UNREAD, Translation :: get('MarkSelectedUnread'), false);
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>
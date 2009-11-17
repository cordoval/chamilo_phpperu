<?php
/**
 * $Id: profile_publication_browser_table.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component.profile_publication_browser
 */
require_once dirname(__FILE__) . '/profile_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/profile_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/profile_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../profiler_manager.class.php';
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
        
        $actions[] = new ObjectTableFormAction(ProfilerManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>
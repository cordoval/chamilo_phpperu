<?php
/**
 * $Id: repository_browser_gallery_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/repository_browser_gallery_table_data_provider.class.php';
require_once dirname(__FILE__) . '/repository_browser_gallery_table_property_model.class.php';
require_once dirname(__FILE__) . '/repository_browser_gallery_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositoryBrowserGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'repository_browser_gallery_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function RepositoryBrowserGalleryTable($browser, $parameters, $condition)
    {
        $property_model = new RepositoryBrowserGalleryTablePropertyModel();
        $cell_renderer = new RepositoryBrowserGalleryTableCellRenderer($browser);
        $data_provider = new RepositoryBrowserGalleryTableDataProvider($browser, $condition);

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer, $property_model);

//        $action = new ObjectTableFormActions();
//
//        if (get_class($browser) == 'RepositoryManagerBrowserComponent')
//        {
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_RECYCLE_CONTENT_OBJECTS, Translation :: get('RemoveSelected')));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_MOVE_CONTENT_OBJECTS, Translation :: get('MoveSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_PUBLISH_CONTENT_OBJECT, Translation :: get('PublishSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, Translation :: get('ExportSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, Translation :: get('EditSelectedRights'), false));
//
//            if ($browser->get_user()->is_platform_admin())
//            {
//                $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_COPY_CONTENT_OBJECT_TO_TEMPLATES, Translation :: get('CopySelectedToTemplates'), false));
//            }
//
//        }
//        if (get_class($browser) == 'RepositoryManagerComplexBrowserComponent')
//        {
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: PARAM_ADD_OBJECTS, Translation :: get('AddObjects'), false));
//        }
//
//        $this->set_form_actions($action);

        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        $this->set_additional_parameters($parameters);
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>
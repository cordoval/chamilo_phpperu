<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\GalleryObjectTable;

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

        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        $this->set_additional_parameters($parameters);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>
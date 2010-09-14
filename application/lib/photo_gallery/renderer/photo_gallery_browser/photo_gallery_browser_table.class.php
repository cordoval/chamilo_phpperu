<?php
require_once dirname(__FILE__) . '/photo_gallery_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/photo_gallery_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/photo_gallery_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../photo_gallery_manager/photo_gallery_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class PhotoGalleryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'photo_gallery_browser_table';

    /**
     * Constructor
     */
    function PhotoGalleryBrowserTable($browser, $parameters, $condition)
    {
        $model = new PhotoGalleryBrowserTableColumnModel();
        $renderer = new PhotoGalleryBrowserTableCellRenderer($browser);
        $data_provider = new PhotoGalleryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, PhotoGalleryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        //$actions = array();
        
        //$actions[] = new ObjectTableFormAction(PhotoGalleryManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>
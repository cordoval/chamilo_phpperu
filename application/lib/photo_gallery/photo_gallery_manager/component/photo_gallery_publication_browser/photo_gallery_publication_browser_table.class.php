<?php
/**
 * $Id: photo_gallery_publication_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.photo_gallery.photo_gallery_manager.component.photo_gallery_publication_browser
 */
require_once dirname(__FILE__) . '/photo_gallery_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/photo_gallery_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/photo_gallery_publication_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of photo_gallery_publications
 *
 * @author Sven Vanpoucke
 * @author 
 */
class PhotoGalleryPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'photo_gallery_publication_browser_table';

    /**
     * Constructor
     */
    function PhotoGalleryPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new PhotoGalleryPublicationBrowserTableColumnModel();
        $renderer = new PhotoGalleryPublicationBrowserTableCellRenderer($browser);
        $data_provider = new PhotoGalleryPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(PhotoGalleryManager :: PARAM_ACTION);
        
        $actions->add_form_action(new ObjectTableFormAction(PhotoGalleryManager :: ACTION_DELETE_PUBLICATION, Translation :: get('RemoveSelected')));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
    
    function handle_table_action()
    {
    	$ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
    	Request :: set_get(PhotoGalleryManager :: PARAM_PUBLICATION, $ids);
    }
}
?>
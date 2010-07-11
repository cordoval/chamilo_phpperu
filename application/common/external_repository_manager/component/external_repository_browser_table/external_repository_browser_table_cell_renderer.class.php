<?php
require_once dirname (__FILE__) . '/../../table/default_external_repository_object_table_cell_renderer.class.php';

class ExternalRepositoryBrowserTableCellRenderer extends DefaultExternalRepositoryObjectTableCellRenderer
{
/**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ExternalRepositoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }
    
 	function render_cell($object)
    {
        $html = array();
        $html[] = '<div style="width:20px;float:right;">';
        $html[] = $this->get_modification_links($object);
        $html[] = '</div>';
        $html[] = '<h3>' . Utilities ::truncate_string($object->get_title(), 25) . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) .')</h3>';
        $html[] = '<a href="' . $this->browser->get_external_repository_object_viewing_url($object) . '"><img class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . Utilities ::truncate_string($object->get_description(), 100) . '</i><br/>';
        return implode("\n", $html);
    }
    
    function get_modification_links($object)
    {
    	$toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);
		$id = $object->get_id();
    	
        if ($this->browser->get_parent()->is_editable($id))
        {
            $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_EDIT_STREAMING_MEDIA, ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
            $toolbar->add_item($toolbar_item_edit);
            
        	$toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_DELETE_STREAMING_MEDIA, ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
        	$toolbar->add_item($toolbar_item_delete);
        }
        
        if ($object->is_usable() && $object->get_url() != null){
	        if ($this->browser->get_parent()->is_stand_alone())
	        {
	            $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->browser->get_url(array(ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_SELECT_STREAMING_MEDIA, ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
	            $toolbar->add_item($toolbar_item_select);
	        }
	        else
	        {
	            $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->browser->get_url(array(ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_IMPORT_STREAMING_MEDIA, ExternalRepositoryManager :: PARAM_STREAMING_MEDIA_ID => $id)), ToolbarItem::DISPLAY_ICON);
	            $toolbar->add_item($toolbar_item_select);       
	        }
        }
        
        return $toolbar->as_html();
    }
}
?>
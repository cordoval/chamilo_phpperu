<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/settings_manager_table_column_model.class.php';
//require_once Path :: get_common_path().'html/table/object_table/object_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class SettingsManagerTableCellRenderer extends ObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param MediamosaStreamingMediaManagerSettingsManagerComponent $component
     */
    function SettingsManagerTableCellRenderer($component)
    {
        $this->component = $component;
    }

    // Inherited
    function render_cell($column, $server_setting)
    {
        if ($column === SettingsManagerTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($server_setting);
        }

        switch ($column->get_name())
        {
           // case ContentObject :: PROPERTY_TYPE :
             //   return '<a href="' . htmlentities($this->component->get_type_filter_url($server_setting->get_type())) . '">' . parent :: render_cell($column, $server_setting) . '</a>';
            case StreamingMediaServerObject :: PROPERTY_TITLE :
                
                return '<a href="' . htmlentities($this->component->get_server_viewing_url($server_setting)) . '" title="' . $server_setting->get_title() . '">' . $server_setting->get_title() . '</a>';
                break;
            case StreamingMediaServerObject :: PROPERTY_ID :
                return $server_setting->get_id();
            break;
            case StreamingMediaServerObject :: PROPERTY_TITLE :
                return $server_setting->get_title();
            break;
            case StreamingMediaServerObject :: PROPERTY_URL :
                return '<a href="' . $server_setting->get_url() . '">' . $server_setting->get_url() . '</a>';
            break;
            case StreamingMediaServerObject :: PROPERTY_LOGIN :
                return $server_setting->get_login();
            break;
            case StreamingMediaServerObject :: PROPERTY_PASSWORD :
                return $server_setting->get_password();
            break;
            case StreamingMediaServerObject:: PROPERTY_IS_UPLOAD_POSSIBLE :
                return $server_setting->get_is_upload_possible();
            break;
            default :
                return '&nbsp;';
        }
        return parent :: render_cell($column, $server_setting);
    }

    function render_id_cell($object){}

    /**
     * Gets the action links to display
     * @param StreamingMediaServerObject $server_setting The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($server_setting)
    {
        if (get_class($this->component) == 'MediamosaStreamingMediaManagerSettingsManagerComponent')
        {
            $toolbar = new Toolbar();
            
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->component->get_server_editing_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));

            if ($url = $this->component->get_server_recycling_url($server_setting))
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Remove'),
        			Theme :: get_common_image_path().'action_recycle_bin.png', 
					$url,
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
				));
            }
           

            
            /*$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Move'),
        			Theme :: get_common_image_path().'action_move.png', 
					$this->component->get_content_object_moving_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));
			
			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Metadata'),
        			Theme :: get_common_image_path().'action_metadata.png', 
					$this->component->get_content_object_metadata_editing_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));
			
			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Rights'),
        			Theme :: get_common_image_path().'action_rights.png', 
					$this->component->get_content_object_rights_editing_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));
			
			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Export'),
        			Theme :: get_common_image_path().'action_export.png', 
					$this->component->get_content_object_exporting_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));

			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Publish'),
        			Theme :: get_common_image_path().'action_publish.png', 
					$this->component->get_publish_content_object_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
			));
		if ($this->component->get_user()->is_platform_admin())
            {
           		$toolbar->add_item(new ToolbarItem(
        			Translation :: get('CopyToTemplates'),
        			Theme :: get_common_image_path().'export_template.png', 
					$this->component->get_copy_content_object_url($server_setting->get_id(), 0),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }

            if ($server_setting->is_complex_content_object())
            {   
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('BrowseComplex'),
        			Theme :: get_common_image_path().'action_build.png', 
					$this->component->get_browse_complex_content_object_url($server_setting),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }

            if($server_setting->get_type() == Document :: get_type_name())
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Export'),
        			Theme :: get_common_image_path().'action_download.png', 
					$this->component->get_document_downloader_url($server_setting->get_id()),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }*/
            return $toolbar->as_html();
        }
        elseif (get_class($this->component) == 'MediamosaStreamingMediaManagerSettingsManagerComponent')
        {
            $toolbar = new Toolbar();

            $params = array();
            $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_ADD_SETTING;

           	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Add'),
        			Theme :: get_common_image_path().'action_add.png', 
					$this->component->get_url($params),
				 	ToolbarItem :: DISPLAY_ICON
				));

            return $toolbar->as_html();
        }
        else
        {
            return '';
        }
    }
}
?>
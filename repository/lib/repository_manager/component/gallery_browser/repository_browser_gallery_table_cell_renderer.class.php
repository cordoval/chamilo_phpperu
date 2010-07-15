<?php
/**
 * $Id: repository_browser_gallery_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_gallery_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryBrowserGalleryTableCellRenderer extends DefaultContentObjectGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositoryBrowserGalleryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($content_object)
    {
        $html = array();
        $html[] = '<div style="width: 20px; float: right;">';
        $html[] = $this->get_modification_links($content_object);
        $html[] = '</div>';
        $html[] = parent :: render_cell($content_object);
        return implode("\n", $html);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    /**
     * @param ContentObject $content_object
     */
    function get_cell_content(ContentObject $content_object)
    {
        $display = ContentObjectDisplay :: factory($content_object);

        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25) . '</h4>';
        $html[] = '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '">' . $display->get_preview(true) . '</a>';

        return implode("\n", $html);
    }

	private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();

//        if (get_class($this->browser) == 'RepositoryManagerBrowserComponent')
//        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png',
					$this->browser->get_content_object_editing_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));

            if ($url = $this->browser->get_content_object_recycling_url($content_object))
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Remove'),
        			Theme :: get_common_image_path().'action_recycle_bin.png',
					$url,
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
				));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('RemoveNA'),
        			Theme :: get_common_image_path().'action_recycle_bin_na.png',
					null,
				 	ToolbarItem :: DISPLAY_ICON
				));
            }
            if ($this->browser->count_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->browser->get_user_id())) > 0)
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Move'),
        			Theme :: get_common_image_path().'action_move.png',
					$this->browser->get_content_object_moving_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }
            else
            {
            	//$toolbar_data[] = array('label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move_na.png');
            }


            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Move'),
        			Theme :: get_common_image_path().'action_move.png',
					$this->browser->get_content_object_moving_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));

			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Metadata'),
        			Theme :: get_common_image_path().'action_metadata.png',
					$this->browser->get_content_object_metadata_editing_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));

			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Rights'),
        			Theme :: get_common_image_path().'action_rights.png',
					$this->browser->get_content_object_rights_editing_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));

			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Export'),
        			Theme :: get_common_image_path().'action_export.png',
					$this->browser->get_content_object_exporting_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));

			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Publish'),
        			Theme :: get_common_image_path().'action_publish.png',
					$this->browser->get_publish_content_object_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));


            if ($this->browser->get_user()->is_platform_admin())
            {
           		$toolbar->add_item(new ToolbarItem(
        			Translation :: get('CopyToTemplates'),
        			Theme :: get_common_image_path().'export_template.png',
					$this->browser->get_copy_content_object_url($content_object->get_id(), 0),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }

            if ($content_object instanceof ComplexContentObjectSupport)
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('BrowseComplex'),
        			Theme :: get_common_image_path().'action_build.png',
					$this->browser->get_browse_complex_content_object_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }

            if($content_object->get_type() == Document :: get_type_name())
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Export'),
        			Theme :: get_common_image_path().'action_download.png',
					$this->browser->get_document_downloader_url($content_object->get_id()),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }
//        }
//        elseif (get_class($this->browser) == 'RepositoryManagerComplexBrowserComponent')
//        {
//           	$toolbar->add_item(new ToolbarItem(
//        			Translation :: get('Add'),
//        			Theme :: get_common_image_path().'action_add.png',
//					$this->browser->get_add_content_object_url($content_object, $this->browser->get_cloi_id(), $this->browser->get_root_id()),
//				 	ToolbarItem :: DISPLAY_ICON
//				));
//        }

        return $toolbar->as_html();
    }
}
?>
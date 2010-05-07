<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/repository_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === RepositoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }

        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return '<a href="' . htmlentities($this->browser->get_type_filter_url($content_object->get_type())) . '">' . parent :: render_cell($column, $content_object) . '</a>';
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        if (get_class($this->browser) == 'RepositoryManagerBrowserComponent')
        {
            $toolbar_data = array();
            $toolbar_data[] = array('href' => $this->browser->get_content_object_editing_url($content_object), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');

            if ($url = $this->browser->get_content_object_recycling_url($content_object))
            {
                $toolbar_data[] = array('href' => $url, 'label' => Translation :: get('Remove'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'confirm' => true);
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('Remove'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png');
            }
            if ($this->browser->count_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->browser->get_user_id())) > 0)
            {
                $toolbar_data[] = array('href' => $this->browser->get_content_object_moving_url($content_object), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
            }
            else
            {
            	//$toolbar_data[] = array('label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move_na.png');
            }
            
            $toolbar_data[] = array('href' => $this->browser->get_content_object_metadata_editing_url($content_object), 'label' => Translation :: get('Metadata'), 'img' => Theme :: get_common_image_path() . 'action_metadata.png');
            $toolbar_data[] = array('href' => $this->browser->get_content_object_rights_editing_url($content_object), 'label' => Translation :: get('Rights'), 'img' => Theme :: get_common_image_path() . 'action_rights.png');
            $toolbar_data[] = array('href' => $this->browser->get_content_object_exporting_url($content_object), 'img' => Theme :: get_common_image_path() . 'action_export.png', 'label' => Translation :: get('Export'));
            $toolbar_data[] = array('href' => $this->browser->get_publish_content_object_url($content_object), 'img' => Theme :: get_common_image_path() . 'action_publish.png', 'label' => Translation :: get('Publish'));

            if ($this->browser->get_user()->is_platform_admin())
            {
                $toolbar_data[] = array('href' => $this->browser->get_copy_content_object_url($content_object->get_id(), 0), 'img' => Theme :: get_common_image_path() . 'export_template.png', 'label' => Translation :: get('CopyToTemplates'));
            }

            if ($content_object->is_complex_content_object())
            {
                $toolbar_data[] = array('href' => $this->browser->get_browse_complex_content_object_url($content_object), 'img' => Theme :: get_common_image_path() . 'action_build.png', 'label' => Translation :: get('BrowseComplex'));
            }

            if($content_object->get_type() == Document :: get_type_name())
            {
            	$toolbar_data[] = array('href' => $this->browser->get_document_downloader_url($content_object->get_id()), 'img' => Theme :: get_common_image_path() . 'action_download.png', 'label' => Translation :: get('Export'));
            }
            
            return Utilities :: build_toolbar($toolbar_data);
        }
        elseif (get_class($this->browser) == 'RepositoryManagerComplexBrowserComponent')
        {
            $toolbar_data = array();
            $toolbar_data[] = array('href' => $this->browser->get_add_content_object_url($content_object, $this->browser->get_cloi_id(), $this->browser->get_root_id()), 'label' => Translation :: get('Add'), 'img' => Theme :: get_common_image_path() . 'action_add.png');

            return Utilities :: build_toolbar($toolbar_data);
        }
        else
        {
            return '';
        }
    }
}
?>
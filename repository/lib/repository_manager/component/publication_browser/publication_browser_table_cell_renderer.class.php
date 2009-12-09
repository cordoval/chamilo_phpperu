<?php
/**
 * $Id: publication_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_browser
 */
require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../publication_table/default_publication_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PublicationBrowserTableCellRenderer extends DefaultPublicationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === PublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case ContentObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE :
                return Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_publication_date());
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
        $toolbar_data = array();
        
        $delete_url = $this->browser->get_content_object_delete_publications_url($content_object);
        $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
    	if (! $content_object->get_publication_object()->is_latest_version())
        {
            $update_url = $this->browser->get_publication_update_url($content_object);
            $toolbar_data[] = array('href' => $update_url, 'label' => Translation :: get('Update'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_revert.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
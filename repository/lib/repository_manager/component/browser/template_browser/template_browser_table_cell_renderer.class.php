<?php
/**
 * $Id: template_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.template_browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class TemplateBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function TemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === TemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
            /*$title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return $title_short;*/
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_modification_date());
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
        $toolbar_data[] = array('href' => $this->browser->get_copy_content_object_url($content_object->get_id(), $this->browser->get_user_id()), 'img' => Theme :: get_common_image_path() . 'export_unknown.png', 'label' => Translation :: get('CopyToRepository'));
        
        if ($this->browser->get_user()->is_platform_admin())
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_template_url($content_object->get_id()), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'label' => Translation :: get('DeleteFromTemplates'));
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
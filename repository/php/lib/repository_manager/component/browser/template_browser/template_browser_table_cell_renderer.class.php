<?php
/**
 * $Id: template_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.template_browser
 */

require_once dirname(__FILE__) . '/../../../../content_object_table/default_content_object_table_cell_renderer.class.php';

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
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_modification_date());
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
    	$toolbar = new Toolbar();
    	
    	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('CopyToRepository'),
        			Theme :: get_common_image_path().'action_copy.png', 
					$this->browser->get_copy_content_object_url($content_object->get_id(), $this->browser->get_user_id()),
				 	ToolbarItem :: DISPLAY_ICON
		));

        if ($this->browser->get_user()->is_platform_admin())
        {
          	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('DeleteFromTemplates'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_delete_template_url($content_object->get_id()),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
			));
        }

        return $toolbar->as_html();
    }
}
?>
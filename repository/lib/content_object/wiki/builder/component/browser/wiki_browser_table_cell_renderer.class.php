<?php
/**
 * $Id: wiki_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class WikiBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function WikiBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    // Inherited
    function render_cell($column, $complex_content_object_item)
    {
        if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($complex_content_object_item)->as_html();
        }

        $content_object = $this->retrieve_content_object($complex_content_object_item->get_ref());

        switch ($column->get_name())
        {
            case Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
                return $content_object->get_title() . ($complex_content_object_item->get_is_homepage() ? ' (' . Translation :: get('HomePage') . ')' : '');
        }

        return parent :: render_cell($column, $complex_content_object_item);
    }

    function get_modification_links($complex_content_object_item)
    {
        $toolbar = parent :: get_modification_links($complex_content_object_item);
       
        if (! $complex_content_object_item->get_is_homepage())
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('SelectAsHomepage'),
        			Theme :: get_common_image_path().'action_home.png',
					$this->browser->get_select_homepage_url($complex_content_object_item),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
				));
        }
        else
        {
           $toolbar->add_item(new ToolbarItem(
        			Translation :: get('SelectedIsHomepage'),
        			Theme :: get_common_image_path().'action_home_na.png',
					null,
				 	ToolbarItem :: DISPLAY_ICON
				));
        }

        return $toolbar;
    }
}
?>
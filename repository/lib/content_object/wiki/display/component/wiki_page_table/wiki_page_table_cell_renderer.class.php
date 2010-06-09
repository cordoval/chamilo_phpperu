<?php
/**
 * $Id: wiki_page_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component.wiki_page_table
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/wiki_page_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class WikiPageTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    private $table_actions;
    private $browser;
    private $datamanager;
    private $publication_id;
    private $complex_id;

    /**
     * Constructor.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     */
    function WikiPageTableCellRenderer($browser)
    {
        $this->table_actions = array();
        $this->browser = $browser;
        $this->datamanager = RepositoryDataManager :: get_instance();

    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === WikiPageTableColumnModel :: get_action_column())
        {
            return $this->get_actions($publication);
        }

        $this->publication_id = Request :: get('publication_id');

        $wiki_page = $this->get_publication_from_complex_content_object_item($publication);
        $this->complex_id = $publication->get_id();

        if ($publication->get_additional_property('is_homepage') == 1)
        {
            $homepage = ' (' . Translation :: get('homepage') . ')';
        }

        if (isset($wiki_page))
        {
            if ($property = $column->get_name())
            {
                switch ($property)
                {
                    case ContentObject :: PROPERTY_TITLE :
                        return '<a href="' . $this->browser->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)) . '">' . htmlspecialchars($wiki_page->get_title()) . '</a>' . $homepage;
                    case Translation :: get('Versions') :
                        return $wiki_page->get_version_count();
                    case ContentObject :: PROPERTY_DESCRIPTION :
                        $description = str_ireplace(']]', '', str_ireplace('[[', '', str_ireplace('=', '', $wiki_page->get_description())));
                        return Utilities :: truncate_string($description, 50);
                }
            }
        }

        return parent :: render_cell($column, $wiki_page);
    }

    function get_actions($publication)
    {
    	$toolbar = New Toolbar();
        if ($this->browser->get_parent()->is_allowed(DELETE_RIGHT))
        {
      		$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $publication->get_id())),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
			));
        }

        if ($this->browser->get_parent()->is_allowed(EDIT_RIGHT))
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->browser->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $publication->get_id())),
				 	ToolbarItem :: DISPLAY_ICON
			));

            if (($publication->get_additional_property('is_homepage') == 0))
            {
        		$toolbar->add_item(new ToolbarItem(
        			Translation :: get('SetAsHomepage'),
        			Theme :: get_common_image_path().'action_home.png', 
					$this->browser->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_SET_AS_HOMEPAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)),
				 	ToolbarItem :: DISPLAY_ICON
				));
            }
            else
            {
        		$toolbar->add_item(new ToolbarItem(
        			Translation :: get('SetAsHomepage'),
        			Theme :: get_common_image_path().'action_home_na.png', 
        			null,
				 	ToolbarItem :: DISPLAY_ICON
				));
            }
        }

        if (count($actions) > 0)
            return $toolbar->as_html();
    }

    /**
     * Gets the links to publish or edit and publish a learning object.
     * @param ContentObject $wiki_page The learning object for which the
     * links should be returned.
     * @return string A HTML-representation of the links.
     */
    private function get_publish_links($wiki_page)
    {
        /*$toolbar_data = array();
        $table_actions = $this->table_actions;

        foreach ($table_actions as $table_action)
        {
            $table_action['href'] = sprintf($table_action['href'], $wiki_page->get_id());
            $toolbar_data[] = $table_action;
        }

        return Utili ties :: build_toolbar($toolbar_data);*/
    }

    private function get_publication_from_complex_content_object_item($clo_item)
    {
        $publication = $this->datamanager->retrieve_content_objects(new EqualityCondition(ContentObject :: PROPERTY_ID, $clo_item->get_default_property(ComplexContentObjectItem :: PROPERTY_REF), ContentObject :: get_table_name()))->as_array();
        return $publication[0];
    }
}
?>
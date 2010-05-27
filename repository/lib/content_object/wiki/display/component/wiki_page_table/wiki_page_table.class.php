<?php
/**
 * $Id: wiki_page_table.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component.wiki_page_table
 */
require_once dirname(__FILE__) . '/wiki_page_table_data_provider.class.php';
require_once dirname(__FILE__) . '/wiki_page_table_column_model.class.php';
require_once dirname(__FILE__) . '/wiki_page_table_cell_renderer.class.php';
//require_once dirname(__FILE__).'/../../../../content_object_publication_table.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class WikiPageTable extends ObjectTable
{
    const DEFAULT_NAME = 'page_table';

    /**
     * Constructor.
     * @param int $owner The id of the current user.
     * @param array $types The types of objects that can be published in current
     * location.
     * @param string $query The search query, or null if none.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     * @see PublicationCandidateTableCellRenderer::PublicationCandidateTableCellRenderer()
     */
    function WikiPageTable($parent, $owner)
    {
        
        $model = new WikiPageTableColumnModel();
        $renderer = new WikiPageTableCellRenderer($parent);
        $data_provider = new WikiPageTableDataProvider($parent, $owner);
        parent :: __construct($data_provider, WikiPageTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(WikiDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, Translation :: get('RestoreSelected'));
        $this->set_form_actions($actions);
    
    }

    /**
     * You should not be concerned with this method. It is only public because
     * of technical limitations.
     */
    function get_objects($offset, $count, $order_column)
    {
        $objects = $this->get_data_provider()->get_objects($offset, $count, /*$this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0))*/null)->as_array();
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        foreach ($objects as $object)
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $object->get_id();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
            }
            $table_data[] = $row;
        }
        return $table_data;
    
    }
}
?>
<?php
/**
 * $Id: doubles_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.doubles_browser
 */

require_once dirname(__FILE__) . '/../../../../content_object_table/default_content_object_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class DoublesBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;
    private $is_detail;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function DoublesBrowserTableCellRenderer($browser, $is_detail)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->is_detail = $is_detail;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === DoublesBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
 
    	switch ($column->get_name())
        {
        	case 'Duplicates';
        		return $content_object->get_content_hash();
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
        if($this->is_detail)
        {
        	return '&nbsp;';
        }
        
    	$toolbar = array();
        
        $view_item = array();
        $view_item['href'] = $this->browser->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
        $view_item['label'] = Translation :: get('ViewItem');
        $view_item['img'] = Theme :: get_common_image_path() . 'action_browser.png';
        
        $toolbar[] = $view_item;
    	return Utilities :: build_toolbar($toolbar);
    }
}
?>
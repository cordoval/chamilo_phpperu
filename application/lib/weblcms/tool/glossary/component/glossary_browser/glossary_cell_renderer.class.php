<?php
/**
 * $Id: glossary_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary.component.glossary_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class GlossaryCellRenderer extends ObjectPublicationTableCellRenderer
{

    function GlossaryCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return Utilities :: build_toolbar($this->get_actions($publication));
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $lo = $publication->get_content_object();
                $feedback_url = $this->browser->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'));
                $data = '<a href="' . $feedback_url . '">' . $lo->get_title() . '</a> ';
                break;
        }
        
        if ($data)
        {
            if ($publication->is_hidden())
            {
                return '<span style="color: gray">' . $data . '</span>';
            }
            else
            {
                return $data;
            }
        }
        else
        {
            return parent :: render_cell($column, $publication);
        }
    }

    function get_actions($publication)
    {
        $actions = parent :: get_actions($publication);
        
        unset($actions['move']);
        
        $feedback_url = $this->browser->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'));
        $actions['feedback'] = array('href' => $feedback_url, 'label' => Translation :: get('Feedback'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        
        return $actions;
    }

}
?>
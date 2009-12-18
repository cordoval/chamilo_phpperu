<?php
/**
 * $Id: document_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component.document_viewer
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class DocumentCellRenderer extends ObjectPublicationTableCellRenderer
{

    function DocumentCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $lo = $publication->get_content_object();
                $feedback_url = $this->browser->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'));
                $data = '<div style="float: left;"><a href="' . $feedback_url . '">' . Utilities :: truncate_string($lo->get_title(),50) . '</a></div> ';
                $url = RepositoryManager :: get_document_downloader_url($lo->get_id());
                $data .= '<div style="float: right;"><a href="' . $url . '">' . Theme :: get_common_image('action_export') . '</a></div>';
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

}
?>
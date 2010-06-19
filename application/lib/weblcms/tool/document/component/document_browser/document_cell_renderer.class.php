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

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $content_object = $publication->get_content_object();
                $details_url = $this->table_renderer->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_VIEW));
                $download_url = RepositoryManager :: get_document_downloader_url($content_object->get_id());

                $data = array();

                if ($publication->is_hidden())
                {
                    $icon = 'action_export_na';
                    $data[] = '<div class="invisible">';
                }
                else
                {
                    $icon = 'action_export';
                }

                $data[] = '<div style="float: left;" title="' . $content_object->get_title() . '">';
                $data[] = '<a href="' . $details_url . '">' . Utilities :: truncate_string($content_object->get_title(), 50) . '</a>';
                $data[] = '</div> ';
                $data[] = '<div style="float: right;">';
                $data[] = '<a href="' . $download_url . '">' . Theme :: get_common_image($icon) . '</a>';
                $data[] = '</div>';

                if ($publication->is_hidden())
                {
                    $data[] = '</div>';
                }

                return implode("\n", $data);
                break;
        }

        return parent :: render_cell($column, $publication);
    }
}
?>
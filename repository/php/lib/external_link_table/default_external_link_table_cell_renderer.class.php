<?php
/**
 * $Id: default_external_link_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.link_table
 */

/**
 * TODO: Add comment
 */
class DefaultExternalLinkTableCellRenderer extends ObjectTableCellRenderer
{
	private $browser;

    /**
     * Constructor
     */
    function DefaultExternalLinkTableCellRenderer($browser)
    {
    	$this->browser = $browser;
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param ContentObject $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $object)
    {
        $external_repository = $object->get_external_repository();
    	switch ($column->get_name())
        {
            case ExternalRepository :: PROPERTY_TYPE :
                $type = $external_repository->get_type();
                return '<img src="' . Theme :: get_common_image_path() . 'external_repository/' . $type . '/logo/22.png" alt="' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($type))) . '"/>';
            case ExternalRepository :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_repository->get_title(), 50);
            case ExternalRepository :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_repository->get_description(), 50);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
<?php
namespace repository;

use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;

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
    function __construct($browser)
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
        $external_instance = $object->get_external_repository();
        switch ($column->get_name())
        {
            case ExternalInstance :: PROPERTY_TYPE :
                return '<img src="' . Theme :: get_image_path(ExternalInstanceManager :: get_namespace($external_instance->get_instance_type(), $external_instance->get_type())) . 'logo/22.png" alt="' . htmlentities(Translation :: get('TypeName', null, ExternalInstanceManager :: get_namespace($external_instance->get_instance_type(), $external_instance->get_type()))) . '"/>';
            case ExternalInstance :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_instance->get_title(), 50);
            case ExternalInstance :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_instance->get_description(), 50);
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
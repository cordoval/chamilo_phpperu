<?php
/**
 * $Id: default_profile_publication_table_cell_renderer.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profile_publication_table
 */

require_once dirname(__FILE__) . '/../profile_publication.class.php';

class DefaultProfilePublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultProfilePublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ProfileTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $profile_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $profile_publication)
    {
        $user = $profile_publication->get_publication_publisher();
        switch ($column->get_name())
        {
            case ProfilePublication :: PROPERTY_PROFILE :
                return $profile_publication->get_publication_object()->get_title();
            case User :: PROPERTY_USERNAME :
                return $user->get_username();
            case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();
            case User :: PROPERTY_FIRSTNAME :
                return $user->get_firstname();
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
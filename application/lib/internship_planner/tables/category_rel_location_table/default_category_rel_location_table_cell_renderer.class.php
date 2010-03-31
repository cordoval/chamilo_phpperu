<?php
/**
 * $Id: default_category_rel_location_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipPlannerCategoryRelLocationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerCategoryRelLocationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $categoryrellocation)
    {
        
        $columns[] = new ObjectTableColumn(InternshipPlannerOrganisation :: PROPERTY_NAME, true, $organisation_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION, true, $organisation_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation :: PROPERTY_NAME, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation :: PROPERTY_CITY, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation :: PROPERTY_STREET, true, $location_alias);
        
        $location_id = $categoryrellocation->get_location_id();
        $location = InternshipPlannerDataManager :: get_instance()->retrieve_location($location_id);
        $organisation = $location->get_organisation();
        
        switch ($column->get_name())
        {
            case InternshipPlannerOrganisation :: PROPERTY_NAME :
                return $organisation->get_name();
            case InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION :
                return $organisation->get_description();
            case InternshipPlannerLocation :: PROPERTY_NAME :
                return $location->get_name();
            case InternshipPlannerLocation :: PROPERTY_CITY :
                return $location->get_city();
            case InternshipPlannerLocation :: PROPERTY_STREET :
                return $location->get_street();
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
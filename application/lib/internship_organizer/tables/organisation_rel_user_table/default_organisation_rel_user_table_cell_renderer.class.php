<?php

class DefaultInternshipOrganizerOrganisationRelUserTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultInternshipOrganizerOrganisationRelUserTableCellRenderer()
    {
    }

    function render_cell($column, $organisation_rel_user)
    {
       
        switch ($column->get_name())
        {
           case User :: PROPERTY_LASTNAME :
                return $organisation_rel_user->get_optional_property(User :: PROPERTY_LASTNAME);
            case User :: PROPERTY_FIRSTNAME :
                return $organisation_rel_user->get_optional_property(User :: PROPERTY_FIRSTNAME);
            case User :: PROPERTY_USERNAME :
                return $organisation_rel_user->get_optional_property(User :: PROPERTY_USERNAME);
            case User :: PROPERTY_EMAIL :
                $email = $organisation_rel_user->get_optional_property(User :: PROPERTY_EMAIL);
            	return '<a href="mailto:' . $email . '">' . $email . '</a><br/>';
        }
    
    }

    function render_id_cell($organisation_rel_user)
    {
        return $organisation_rel_user->get_organisation_id() . '|' . $organisation_rel_user->get_user_id();
    }

}
?>
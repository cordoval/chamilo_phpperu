<?php 

namespace application\profiler;

use user\UserDataManager;
use user\User;
/**
 * $Id: default_profile_publication_table_column_model.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profile_publication_table
 */
class DefaultProfilePublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultProfilePublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ProfileTableColumn[]
     */
    private static function get_default_columns()
    {
        $udm = UserDataManager :: get_instance();
        $user_alias = $udm->get_alias(User :: get_table_name());

        $columns = array();
        $columns[] = new ObjectTableColumn(ProfilePublication :: PROPERTY_PROFILE);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        return $columns;
    }
}
?>
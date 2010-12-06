<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * Description of mediamosa_external_repository_user_quotumclass
 *
 * @author jevdheyd
 */
class ExternalRepositoryUserQuotum extends DataClass {

    const CLASS_NAME = __CLASS__;
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_QUOTUM = 'quotum';

    /**
     * Get the default properties of all server_objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_QUOTUM));
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    function set_quotum($quotum)
    {
        $this->set_default_property(self :: PROPERTY_QUOTUM, $quotum);
    }

    function get_quotum()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTUM);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    function get_data_manager(){
        return RepositoryDataManager :: get_instance();
    }
}
?>

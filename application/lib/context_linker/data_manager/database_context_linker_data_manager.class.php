<?php



/**
 * @package context_linker.datamanager
 */
require_once dirname(__FILE__).'/../context_link.class.php';
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../context_linker_data_manager_interface.class.php';
require_once dirname(__FILE__) . '/../../metadata/metadata_property_type.class.php';
require_once dirname(__FILE__) . '/../../metadata/metadata_property_value.class.php';
require_once dirname(__FILE__) . '/../../metadata/metadata_data_manager.class.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author Jens Vanderheyden
 */

class DatabaseContextLinkerDataManager extends Database implements ContextLinkerDataManagerInterface
{
    function initialize()
    {
//            $aliases = array();
//            $aliases[ContextLink :: get_table_name()] = 'conk';

            parent :: initialize();
            $this->set_prefix('context_linker_');
    }

    function get_next_context_link_id()
    {
            return $this->get_next_id(ContextLink :: get_table_name());
    }

    function create_context_link($context_link)
    {
            return $this->create($context_link);
    }

    function update_context_link($context_link)
    {
            $condition = new EqualityCondition(ContextLink :: PROPERTY_ID, $context_link->get_id());
            return $this->update($context_link, $condition);
    }

    function delete_context_link($context_link)
    {
            $condition = new EqualityCondition(ContextLink :: PROPERTY_ID, $context_link->get_id());
            return $this->delete($context_link->get_table_name(), $condition);
    }

    function count_context_links($condition = null)
    {
            return $this->count_objects(ContextLink :: get_table_name(), $condition);
    }

    function retrieve_context_link($id)
    {
            $condition = new EqualityCondition(ContextLink :: PROPERTY_ID, $id);
            return $this->retrieve_object(ContextLink :: get_table_name(), $condition);
    }

    function retrieve_context_links($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
            return $this->retrieve_objects(ContextLink :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_full_context_links($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $mdm = MetadataDataManager :: get_instance();

        $content_object_alias = $rdm->get_alias(ContentObject::get_table_name());
        $property_type_alias = $mdm->get_alias(MetadataPropertyType :: get_table_name());
        $property_value_alias = $mdm->get_alias(MetadataPropertyValue :: get_table_name());
        $context_link_alias = $this->get_alias(ContextLink :: get_table_name());

        $query = 'SELECT ' . $context_link_alias . '.' . ContextLink :: PROPERTY_ID . ',' . $content_object_alias . '.' . ContentObject :: PROPERTY_TYPE . ', ' . $content_object_alias . '.' . ContentObject :: PROPERTY_TITLE . ', ' . $property_type_alias . '.' . MetadataPropertyType :: PROPERTY_NS_PREFIX . ', ' . $property_type_alias . '.' . MetadataPropertyType :: PROPERTY_NAME . ', ' . $property_value_alias . '.' . MetadataPropertyValue :: PROPERTY_VALUE;
        $query .=' FROM ' . $this->escape_table_name(ContextLink :: get_table_name()). ' AS ' . $context_link_alias;
        $query .= ' LEFT JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
        $query .= ' ON ' . $this->escape_column_name(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $context_link_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias);
        $query .= ' LEFT JOIN ' . $mdm->escape_table_name(MetadataPropertyValue :: get_table_name()) . ' AS ' . $property_value_alias;
        $query .= ' ON '. $this->escape_column_name(ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, $context_link_alias).' = ' . $mdm->escape_column_name(MetadataPropertyValue :: PROPERTY_ID, $property_value_alias);
        $query .= ' LEFT JOIN ' . $mdm->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $property_type_alias;
        $query .= ' ON  ' . $mdm->escape_column_name(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $property_value_alias) . '=' . $mdm->escape_column_name(MetadataPropertyType :: PROPERTY_ID, $property_type_alias);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);

        $context_links = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $context_links[] = array(ContextLink :: PROPERTY_ID => $record[ContextLink :: PROPERTY_ID],
                                    ContentObject :: PROPERTY_TYPE => $record[ContentObject :: PROPERTY_TYPE],
                                    ContentObject :: PROPERTY_TITLE => $record[ContentObject :: PROPERTY_TITLE],
                                    MetadataPropertyType :: PROPERTY_NS_PREFIX => $record[MetadataPropertyType :: PROPERTY_NS_PREFIX],
                                    MetadataPropertyType :: PROPERTY_NAME => $record[MetadataPropertyType :: PROPERTY_NAME],
                                    MetadataPropertyValue :: PROPERTY_VALUE => $record[MetadataPropertyValue :: PROPERTY_VALUE]);
        }
        $res->free();
        return new ArrayResultSet($context_links);
    }
}
?>
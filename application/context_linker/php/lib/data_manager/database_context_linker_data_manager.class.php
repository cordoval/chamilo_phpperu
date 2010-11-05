<?php
namespace application\context_linker;
use common\libraries\Database;
use common\libraries\EqualityCondition;
use repository\RepositoryDataManager;
use common\libraries\ConditionTranslator;
use application\metadata\MetadataDataManager;
use repository\ContentObject;
use application\metadata\MetadataPropertyType;
use application\metadata\MetadataPropertyValue;
use application\metadata\ContentObjectMetadataPropertyValue;

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
            return $this->retrieve_object(ContextLink :: get_table_name(), $condition, null, ContextLink :: CLASS_NAME);
    }

    function retrieve_context_links($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
            return $this->retrieve_objects(ContextLink :: get_table_name(), $condition, $offset, $max_objects, $order_by, ContextLink :: CLASS_NAME);
    }

    /*
     * retrieves detailed info on a context link
     * 
     *  @return array(clid, orig_id, orig_type, orig_title, alt_id, alt_type, alt_title, metadata_property_type, name, value, metadata_property_value, date
     */
    function retrieve_full_context_links($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $mdm = MetadataDataManager :: get_instance();

        $orig_content_object_alias = $rdm->get_alias(ContentObject::get_table_name());
        $alt_content_object_alias = $rdm->get_alias(ContentObject::get_table_name()) . '_2';
        $property_type_alias = $mdm->get_alias(MetadataPropertyType :: get_table_name());
        $property_value_alias = $mdm->get_alias(ContentObjectMetadataPropertyValue :: get_table_name());
        $context_link_alias = $this->get_alias(ContextLink :: get_table_name());

        $query = 'SELECT ' . $context_link_alias . '.' . ContextLink :: PROPERTY_ID . ',' . $context_link_alias . '.' . ContextLink :: PROPERTY_DATE . ',
                ' . $orig_content_object_alias . '.' . ContentObject :: PROPERTY_TYPE . ' AS orig_type, '. $orig_content_object_alias . '.' . ContentObject :: PROPERTY_ID . ' AS orig_id, ' . $orig_content_object_alias . '.' . ContentObject :: PROPERTY_TITLE . ' AS orig_title,
                ' . $alt_content_object_alias . '.' . ContentObject :: PROPERTY_TYPE . ' AS alt_type, '. $alt_content_object_alias . '.' . ContentObject :: PROPERTY_ID . ' AS alt_id, ' . $alt_content_object_alias . '.' . ContentObject :: PROPERTY_TITLE . ' AS alt_title,
                ' . $property_type_alias . '.' . MetadataPropertyType :: PROPERTY_NS_PREFIX . ', ' . $property_type_alias . '.' . MetadataPropertyType :: PROPERTY_NAME . ', ' . $property_value_alias . '.' . MetadataPropertyValue :: PROPERTY_VALUE;
        $query .=' FROM ' . $this->escape_table_name(ContextLink :: get_table_name()). ' AS ' . $context_link_alias;
        $query .= ' LEFT JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $orig_content_object_alias;
        $query .= ' ON ' . $this->escape_column_name(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $orig_context_link_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $orig_content_object_alias);
        $query .= ' LEFT JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $alt_content_object_alias;
        $query .= ' ON ' . $this->escape_column_name(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $alt_context_link_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $alt_content_object_alias);
        $query .= ' LEFT JOIN ' . $mdm->escape_table_name(ContentObjectMetadataPropertyValue :: get_table_name()) . ' AS ' . $property_value_alias;
        $query .= ' ON '. $this->escape_column_name(ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID, $context_link_alias).' = ' . $mdm->escape_column_name(MetadataPropertyValue :: PROPERTY_ID, $property_value_alias);
        $query .= ' LEFT JOIN ' .  $mdm->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $property_type_alias;
        $query .= ' ON  ' . $mdm->escape_column_name(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $property_value_alias) . '=' . $mdm->escape_column_name(MetadataPropertyType :: PROPERTY_ID, $property_type_alias);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);

        $context_links = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $context_links[] = array(ContextLink :: PROPERTY_ID => $record[ContextLink :: PROPERTY_ID],
                                    ContextLink :: PROPERTY_DATE => $record[ContextLink :: PROPERTY_DATE],
                                    'orig_' . ContentObject :: PROPERTY_ID => $record['orig_id'],
                                    'orig_' . ContentObject :: PROPERTY_TYPE => $record['orig_type'],
                                    'orig_' . ContentObject :: PROPERTY_TITLE => $record['orig_title'],
                                    'alt_' . ContentObject :: PROPERTY_ID => $record[ContextLinkerManager::PROPERTY_ALT_ID],
                                    'alt_' . ContentObject :: PROPERTY_TYPE => $record['alt_type'],
                                    'alt_' . ContentObject :: PROPERTY_TITLE => $record['alt_title'],
                                    MetadataPropertyType :: PROPERTY_NS_PREFIX => $record[MetadataPropertyType :: PROPERTY_NS_PREFIX],
                                    MetadataPropertyType :: PROPERTY_NAME => $record[MetadataPropertyType :: PROPERTY_NAME],
                                    MetadataPropertyValue :: PROPERTY_VALUE => $record[MetadataPropertyValue :: PROPERTY_VALUE]);
        }
        $res->free();
        return $context_links;
    }

    /*
     * recursively look for connections for a content object
     *
     * @return array[n] = output from retrieve_full_context_links
     */
    function retrieve_full_context_links_recursive($condition = null, $offset = null, $max_objects = null, $order_by = null, $ids = array())
    {
        $context_links = $this->retrieve_full_context_links($condition, $offset, $max_objects, $order_by);

        if(count($context_links))
        {
            foreach($context_links as $context_link)
            {
                $condition_orig = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $context_link[ContextLinkerManager::PROPERTY_ALT_ID]);
                $condition_alt = new EqualityCondition(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $context_link[ContextLinkerManager::PROPERTY_ORIG_ID]);

                $result[] = $context_link;

                //downward direction
                //if parent is not used as child (endless loop protection)
		if(!isset($ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]]))
                {
                    $ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]] = 1;
                    $result = array_merge($result, $this->retrieve_full_context_links_recursive($condition_orig, $offset, $max_objects, $order_by, $ids));

                }

                //upward direction
                //if child is not used as parent (endless loop protection)
                if(!isset($ids[$context_link[ContextLinkerManager::PROPERTY_ORIG_ID]]))
                {
                    $ids[$context_link[ContextLinkerManager::PROPERTY_ORIG_ID]] = 1;
                    $result = array_merge($this->retrieve_full_context_links_recursive($condition_alt, $offset, $max_objects, $order_by, $ids), $result);
                }
            }
        }
        else
        {
            $result = $context_links;
        }
        return $result;
    }


}
?>
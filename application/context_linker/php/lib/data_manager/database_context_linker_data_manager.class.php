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

    private $loop_protection = array();

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
                                    ContextLinkerManager::PROPERTY_ORIG_ID => $record['orig_id'],
                                    ContextLinkerManager :: PROPERTY_ORIG_TYPE => $record['orig_type'],
                                    ContextLinkerManager :: PROPERTY_ORIG_TITLE => $record['orig_title'],
                                    ContextLinkerManager::PROPERTY_ALT_ID => $record[ContextLinkerManager::PROPERTY_ALT_ID],
                                    ContextLinkerManager :: PROPERTY_ALT_TYPE => $record['alt_type'],
                                    ContextLinkerManager :: PROPERTY_ALT_TITLE => $record['alt_title'],
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
     * @param $array_type ContextLinkerManager :: ARRAY_TYPE_FLAT(for table display) or ContextLinkerManager :: ARRAY_TYPE_RECURSIVE (for visual display)
     * @param $direction : look for parents (ContextLinkerManager :: RECURSIVE_DIRECTION_UP) or for children (ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN)
     * @return array[n] = output from retrieve_full_context_links
     */
    function retrieve_recursive($condition = null, $offset = null, $max_objects = null, $order_by = null,  $array_type = ContextLinkerManager :: ARRAY_TYPE_FLAT, $direction = ContextLinkerManager :: RECURSIVE_DIRECTION_BOTH)
    {
        $context_links = $this->retrieve_full_context_links($condition, $offset, $max_objects, $order_by);

        if(count($context_links))
        {
            foreach($context_links as $context_link)
            {
                $condition_orig = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $context_link[ContextLinkerManager::PROPERTY_ALT_ID]);
                $condition_alt = new EqualityCondition(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $context_link[ContextLinkerManager::PROPERTY_ORIG_ID]);

                if($array_type == ContextLinkerManager :: ARRAY_TYPE_FLAT)
                {
                    $result[] = $context_link;
                }
                else
                {
                    
                    if($direction == ContextLinkerManager :: RECURSIVE_DIRECTION_UP)
                    {
                        $result[$context_link[ContextLinkerManager :: PROPERTY_ORIG_ID]] = $context_link;
                    }
                    else
                    {
                        $result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]] = $context_link;
                    }
                }

                //downward direction - look for children
                if($direction == ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN)
                {
                    //downward direction
                    //if parent is not used as child (endless loop protection)
                    //if(!isset($ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]]))
                    if(!isset($this->loop_protection[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]]))
                    {
                        //$ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]] = 1;
                        $this->loop_protection[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]] = 1;
                        $result2 = $this->retrieve_recursive($condition_orig, $offset, $max_objects, $order_by,  $array_type, $direction);

                        if($array_type == ContextLinkerManager :: ARRAY_TYPE_FLAT)
                        {
                            $result = array_merge($result, $result2);
                        }
                        else
                        {
                            $new_child = $result2;
                            if(count($new_child))
                            {
                                if(!isset($result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]]['children'])) $result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]]['children'] = array();

                                foreach($new_child as $n => $value)
                                {
                                    if(!isset($result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]]['children'][$n])) $result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]]['children'][$n] = array();
                                    $result[$context_link[ContextLinkerManager :: PROPERTY_ALT_ID]]['children'][$n] = $value;
                                }
                            }
                        }
                    }
                }

                //upward direction - look for parents
                if($direction == ContextLinkerManager :: RECURSIVE_DIRECTION_UP)
                {
                    //if child is not used as parent (endless loop protection)
                    //if(!isset($ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]]))
                        //if(!isset($ids[$context_link[ContextLinkerManager::PROPERTY_ALT_ID]]))
                    if(!isset($this->loop_protection[$context_link[ContextLinkerManager::PROPERTY_ORIG_ID]]))
                    {
                        //$ids[$context_link[ContextLinkerManager::PROPERTY_ORIG_ID]] = 1;
                        
                        $this->loop_protection[$context_link[ContextLinkerManager::PROPERTY_ORIG_ID]]=1;
                        $result2 = $this->retrieve_recursive($condition_alt, $offset, $max_objects, $order_by,  $array_type, $direction);
                        
                        if($array_type == ContextLinkerManager :: ARRAY_TYPE_FLAT)
                        {
                            $result = array_merge($result2, $result);
                        }
                        else
                        {
                            $new_parent = $result2;

                            if(count($new_parent))
                            {
                                if(!isset($result[$context_link[ContextLinkerManager :: PROPERTY_ORIG_ID]]['parents'])) $result[$context_link[ContextLinkerManager :: PROPERTY_ORIG_ID]]['parents'] = array();

                                foreach($new_parent as $n => $value)
                                {
                                    $result[$context_link[ContextLinkerManager :: PROPERTY_ORIG_ID]]['parents'][$n] = $value;
                                
                                    
                                }

                            }
                        }
                    }
                }
            }
        }
        else
        {
            $result = $context_links;
        }
        return $result;
    }

    /*
     * finds all parents and children of a certain context link as far
     * recursively finds parents of parent and children of children
     *
     * @param $content_object_id the id of the central content object
     * @param $array_type ContextLinkerManager :: ARRAY_TYPE_FLAT(for table display) or ContextLinkerManager :: ARRAY_TYPE_RECURSIVE (for visual display)
     *
     * @return array
     * flat = array[n]=output of retrieve_full_context_links
     * recursive
     * array[id]=output of retrieve_full_context_links
     * array[children] = array children
     * array[parents] = array parents
     */
    function retrieve_full_context_links_recursive($content_object_id, $offset = null, $max_objects = null, $order_by = null, $array_type = ContextLinkerManager :: ARRAY_TYPE_FLAT)
    {
        //create conditions
        $condition_down = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $content_object_id);
       
        $condition_up = new EqualityCondition(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $content_object_id);

        //do queries
        $result_children = $this->retrieve_recursive($condition_down, $offset = null, $max_objects = null, $order_by = null,  $array_type, ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN);
        $this->loop_protection = array(); //reset
        $result_parents = $this->retrieve_recursive($condition_up, $offset = null, $max_objects = null, $order_by = null,  $array_type, ContextLinkerManager :: RECURSIVE_DIRECTION_UP);
        
    
        //format result
        if($array_type == ContextLinkerManager :: ARRAY_TYPE_RECURSIVE)
        {
            $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID , $content_object_id);
            $result_tmp = $this->retrieve_full_context_links($condition);
            $result[$content_object_id] = $result_tmp[0];
            unset($result_tmp);
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ORIG_ID] = $orig[ContextLinkerManager :: PROPERTY_ORIG_ID];
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ORIG_TYPE] = $orig[ContextLinkerManager :: PROPERTY_ORIG_TYPE];
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ORIG_TITLE] = $orig[ContextLinkerManager :: PROPERTY_ORIG_TITLE];
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ALT_ID] = $orig[ContextLinkerManager :: PROPERTY_ALT_ID];
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ALT_TYPE] = $orig[ContextLinkerManager :: PROPERTY_ALT_TYPE];
//            $result[$orig[ContextLinkerManager :: PROPERTY_ORIG_ID]][ContextLinkerManager :: PROPERTY_ALT_TITLE] = $orig[ContextLinkerManager :: PROPERTY_ALT_TITLE];

            $result[$content_object_id]['parents'] = $result_parents;
            $result[$content_object_id]['children'] = $result_children;

            return $result;
        }
        else
        {
            return array_merge($result_parents,$result_children);
        }
    }
}
?>
<?php
namespace repository;

use common\libraries\HelperContentObjectSupport;
use common\libraries\ComplexContentObjectSupport;
use common\libraries\EqualityCondition;

class ComplexContentObjectPath
{
    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $children;

    /**
     * @var array
     */
    private $parents;

    /**
     * @param ContentObject $content_object
     */
    function __construct(ContentObject $content_object)
    {
        $this->initialize($content_object);
    }

    /**
     * @return array:
     */
    function get_nodes()
    {
        return $this->nodes;
    }

    /**
     * @param int $parent_id
     * @param mixed $data
     */
    function add($parent_id = 0, $data)
    {
        $node_id = $this->get_next_id();
        $this->nodes[$node_id] = new ComplexContentObjectPathNode($this, $node_id, $parent_id, $data);
        return $node_id;
    }

    /**
     * @return int
     */
    function get_next_id()
    {
        return count($this->nodes) + 1;
    }

    /**
     * @param int $node_id
     * @throws Exception
     * @return ComplexContentObjectPathNode
     */
    function get_node($node_id)
    {
        if (! isset($this->nodes[$node_id]))
        {
            throw new Exception(Translation :: get('NodeDoesntExist'));
        }
        return $this->nodes[$node_id];
    }

    /**
     * @param int $node_id
     * @param boolean $include_self
     * @return array<ComplexContentObjectPathNode>
     */
    function get_parents_by_id($node_id, $include_self = false, $reverse = false)
    {
        if (! isset($this->parents[$node_id][$include_self][$reverse]))
        {
            $this->parents[$node_id][$include_self][$reverse] = ComplexContentObjectPathNode :: get_node_parents($this->get_node($node_id), $include_self, $reverse);
        }

        return $this->parents[$node_id][$include_self][$reverse];
    }

    /**
     * @param int $node_id
     */
    function get_children_by_id($node_id)
    {
        if (! isset($this->children[$node_id]))
        {
            $this->children[$node_id] = ComplexContentObjectPathNode :: get_node_children($this->get_node($node_id));
        }

        return $this->children[$node_id];
    }

    /**
     * @throws Exception
     * @return ComplexContentObjectPathNode
     */
    function get_root()
    {
        foreach ($this->nodes as $node)
        {
            if ($node->is_root())
            {
                return $node;
            }
        }

        throw new Exception(Translation :: get('NoRootNode'));
    }

    /**
     * @return int
     */
    function count_nodes()
    {
        return count($this->nodes);
    }

    /**
     * @param int $id
     * @return NULL|ComplexContentObjectPathNode
     */
    function get_next_node_by_id($id)
    {
        if ($id >= $this->count_nodes())
        {
            return null;
        }
        else
        {
            return $this->get_node($id + 1);
        }
    }

    /**
     * @param int $id
     * @return NULL|ComplexContentObjectPathNode
     */
    function get_previous_node_by_id($id)
    {
        if ($id <= 1)
        {
            return null;
        }
        else
        {
            return $this->get_node($id - 1);
        }
    }

    /**
     * @param ContentObject $content_object
     */
    function initialize(ContentObject $content_object)
    {
        $root_id = $this->add(0, $content_object);
        $this->add_items($root_id, $content_object);
    }

    function add_items($parent_id, ContentObject $root_content_object)
    {
        if ($root_content_object instanceof ComplexContentObjectSupport)
        {
            $data_manager = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $root_content_object->get_id(), ComplexContentObjectItem :: get_table_name());
            $complex_content_object_items = $data_manager->retrieve_complex_content_object_items($condition);

            while (($complex_content_object_item = $complex_content_object_items->next_result()))
            {
                $content_object = $data_manager->retrieve_content_object($complex_content_object_item->get_ref());

                if ($content_object instanceof HelperContentObjectSupport)
                {
                    $content_object = $data_manager->retrieve_content_object($content_object->get_reference());
                }

                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $node_id = $this->add($parent_id, $content_object);
                    $this->add_items($node_id, $content_object);
                }
            }
        }
    }
}
?>
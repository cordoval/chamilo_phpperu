<?php
namespace repository;

class ComplexContentObjectPathNode
{

    /**
     * @var ComplexContentObjectPath
     */
    private $tree;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $parent_id;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $children;

    /**
     * @var array
     */
    private $parents;

    /**
     * @param ComplexContentObjectPath $tree
     * @param int $id
     * @param int $parent
     * @param mixed $data
     */
    function __construct($tree, $id, $parent_id, $data)
    {
        $this->tree = $tree;
        $this->id = $id;
        $this->parent_id = $parent_id;
        $this->data = $data;
    }

    /**
     * @return ComplexContentObjectPath
     */
    function get_tree()
    {
        return $this->tree;
    }

    /**
     * @return int
     */
    function get_id()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    function get_parent_id()
    {
        return $this->parent_id;
    }

    /**
     * @return mixed
     */
    function get_data()
    {
        return $this->data;
    }

    /**
     * @param int $id
     */
    function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $parent
     */
    function set_parent_id($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @param mixed $data
     */
    function set_data($data)
    {
        $this->data = $data;
    }

    /**
     * @return boolean
     */
    function is_root()
    {
        return $this->get_parent_id() == 0;
    }

    function get_parent()
    {
        if ($this->is_root())
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_parent_id());
        }
    }

    /**
     * @param int $node_id
     * @param boolean $include_self
     * @return array<ComplexContentObjectPathNode>
     */
    function get_parents($include_self = false, $reverse = false)
    {
        if (! isset($this->parents[$include_self][$reverse]))
        {
            $this->parents[$include_self][$reverse] = self :: get_node_parents($this, $include_self, $reverse);
        }

        return $this->parents[$include_self][$reverse];
    }

    function get_children()
    {
        if (! isset($this->children))
        {
            $this->children = self :: get_node_children($this);
        }

        return $this->children;
    }

    /**
     * @return NULL|ComplexContentObjectPathNode
     */
    function get_next()
    {
        if ($this->get_id() >= $this->get_tree()->count_nodes())
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_id() + 1);
        }
    }

    /**
     * @return NULL|ComplexContentObjectPathNode
     */
    function get_previous()
    {
        if ($this->get_id() <= 1)
        {
            return null;
        }
        else
        {
            return $this->get_tree()->get_node($this->get_id() - 1);
        }
    }

    static function get_node_parents(ComplexContentObjectPathNode $node, $include_self = false, $reverse = false)
    {
        $parents = array();

        if ($include_self)
        {
            $parents[] = $node;
        }

        while ($node->get_parent_id() !== 0)
        {
            $parents[] = $node;
            $node = $node->get_tree()->get_node($node->get_parent_id());
        }

        $parents[] = $node;

        if ($reverse)
        {
            krsort($reverse);
        }

        return $parents;
    }

    static function get_node_children(ComplexContentObjectPathNode $parent)
    {
        $children = array();

        foreach ($parent->get_tree()->get_nodes() as $node)
        {
            if ($node->get_parent_id() == $parent->get_id())
            {
                $children[] = $node;
            }
        }

        return $children;
    }
}
?>
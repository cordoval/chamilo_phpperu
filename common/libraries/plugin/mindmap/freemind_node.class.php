<?php
/**
 * generates xml for freemind mindmap points
 * @author jens vanderheyden
 */
class FreemindNode
{
    private $id;
    private $created;
    private $text;
    private $modified;
    private $link;
    private $position;
    private $children = array();
    private $icons = array();

    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';

    function __construct($id, $text)
    {
        $this->id = $id;
        $this->text = $text;
        $this->created = time();
    }

    function get_id()
    {
        return $this->id;
    }

    function set_id($id)
    {
        $this->id = $id;
    }

    function get_created()
    {
        return $this->created;
    }

    function set_created($created)
    {
        $this->created = $created;
    }

    function get_modified()
    {
        return $this->modified;
    }

    function set_modified($modified)
    {
        $this->modified = $modified;
    }

    function get_link()
    {
        return $this->link;
    }

    function set_link($link)
    {
        $this->link = $link;
    }

    function get_position()
    {
        return $this->position;
    }

    function set_position($position)
    {
        $this->position = $position;
    }

    function get_children()
    {
        return $this->children;
    }

    function set_children($children)
    {
        $this->children = $children;
    }

    function add_child(FreemindNode $child)
    {
        $this->children[$child->get_id()] = $child;
        return $child;
    }

    function get_child($child_id)
    {
        return $this->children[$child_id];
    }

    function add_icon($title, $link = null)
    {
        if(!array_search($title, $this->icons))
        {
            $this->icons[] = array('title' => $title, 'link' => $link);
        }
    }

    function get_icons()
    {
        return $this->icons;
    }

    function to_xml()
    {
       if($this->modified)
       {
           $id = 'MODIFIED="' . $this->modified . '" ';
       }
       if($this->link)
       {
            $link = 'LINK="' . $this->link . '" ';
       }
       if($this->position){
           $position = 'POSITION="' . $this->position . '" ';
       }

       $icons = '';
       if(count($this->icons))
       {
           foreach($this->icons as $n => $icon)
           {
               $source = is_null($icon['link']) ? 'BUILTIN="' . $icon['title'] . '" ' : 'HREF="' . $icon['link'] . '"';
               $icons .= '<icon '.$source.'/>';
           }
       }

       $start = '<node ' . 'CREATED="' . $this->created . '" ID="' . $this->id . '" ' . $modified . 'TEXT="' . $this->text . '" ' . $position . $link;

       if(count($this->children))
       {
           $children = '';
           foreach($this->children as $id => $child)
           {
               $children .= $child->to_xml();
           }
           return $start . '>' . $children . '</node>' . "\n";
       }
       return $start . '/>' . "\n";
    }
}

?>

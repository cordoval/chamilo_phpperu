<?php

namespace common\libraries;

/**
 * A FS - file system - store. Used to put together several FS objects such as queries.
 *
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_store extends fedora_fs_folder {

    protected $title = '';
    //protected $id = '';
    protected $children = array();
    protected $elements = array();

    public function __construct($title, $fsid = '', $class = '') {
        parent::__construct($fsid);
        $this->title = $title;
        if ($class) {
            $this->class = $class;
        }
    }

    public function aggregate($element) {
        $this->elements[] = $element;
    }

    public function is_aggregate() {
        return count($this->elements) > 0;
    }

    public function add($child) {
        $this->children[$child->get_fsid()] = $child;
    }

    public function add_all($children) {
        foreach ($children as $child) {
            $this->add($child);
        }
    }

    /**
     * Locate a child fs object based on his fs id.
     *
     * @param string $fsid
     * @return fedora_fs_base
     */
    public function find($fsid) {
        if ($fsid == $this->get_fsid()) {
            return $this;
        }
        foreach ($this->children as $child) {
            if ($result = $child->find($fsid)) {
                return $result;
            }
        }

        return false;
    }

    public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false) {
        $result = $this->children;
        foreach ($this->elements as $element) {
            if ($items = $element->query($fedora, $sort, $limit, $offset)) {
                $result = array_merge($result, $items);
            }
        }
        return $result;
    }

    public function count(FedoraProxy $fedora) {
        $result = count($this->children);
        foreach ($this->elements as $element) {
            $result += $element->count($fedora);
        }
        return $result;
    }

    public function get_children() {
        return $this->children;
    }

    public function get_aggregate() {
        return $this->elements;
    }

}

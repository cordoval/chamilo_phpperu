<?php
/**
 * $Id: forumnodedisplay.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum.inc
 */
require_once dirname(__FILE__) . '/forumtable.class.php';

class ForumNodeDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $object = $this->get_content_object();
        $table = new ForumTable($object, $this->get_content_object_url_format());
        $html = array();
        $html[] = parent :: get_full_html();
        $html[] = '<div class="lo_intermediate_header" style="margin: 1em 0 0.5em 0; font-weight: bold; font-size: larger;">' . htmlentities(Translation :: get($object->get_type() == 'forum' ? 'TopicsOnForum' : 'PostsInTopic')) . '</div>';
        $html[] = $table->as_html();
        return implode("\n", $html);
    }
}
?>
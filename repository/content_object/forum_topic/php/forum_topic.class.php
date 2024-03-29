<?php
namespace repository\content_object\forum_topic;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Versionable;
use common\libraries\AttachmentSupport;
use common\libraries\ObjectTableOrder;
use common\libraries\ComplexContentObjectSupport;

use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\content_object\forum_post\ForumPost;

/**
 * $Id: forum_topic.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum_topic
 */
/**
 * This class represents a topic in a discussion forum.
 */
class ForumTopic extends ContentObject implements Versionable, AttachmentSupport, ComplexContentObjectSupport
{
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_TOTAL_POSTS = 'total_posts';
    const PROPERTY_LAST_POST = 'last_post_id';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    private $first_post;

    function create()
    {
        $succes = parent :: create();
        $children = RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id()));

        //@todo: $children should always be null. should be better to remove this part and
        //@todo: eventually move it to the user interface. Creates issue with re-import.
        if ($children == 0)
        {
            $content_object = ContentObject :: factory(ForumPost :: get_type_name());
            $content_object->set_title($this->get_title());
            $content_object->set_description($this->get_description());
            $content_object->set_owner_id($this->get_owner_id());

            $content_object->create();

            $this->first_post = $content_object;

            $cloi = ComplexContentObjectItem :: factory(ForumPost :: get_type_name());

            $cloi->set_ref($content_object->get_id());
            $cloi->set_user_id($this->get_owner_id());
            $cloi->set_parent($this->get_id());
            $cloi->set_display_order(1);

            $cloi->create();
        }

        return $succes;
    }

    function attach_content_object($aid, $type = self :: ATTACHMENT_NORMAL)
    {
        $success = parent :: attach_content_object($aid, $type);

        if ($this->first_post)
        {
            $success &= $this->first_post->attach_content_object($aid, $type);
        }

        return $success;
    }

    function get_locked()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCKED);
    }

    function set_locked($locked)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCKED, self :: PROPERTY_TOTAL_POSTS);
    }

    function get_allowed_types()
    {
        return array(ForumPost :: get_type_name());
    }

    function get_total_posts()
    {
        return $this->get_additional_property(self :: PROPERTY_TOTAL_POSTS);
    }

    function set_total_posts($total_posts)
    {
        $this->set_additional_property(self :: PROPERTY_TOTAL_POSTS, $total_posts);
    }

    function get_last_post()
    {
        return $this->get_additional_property(self :: PROPERTY_LAST_POST);
    }

    function set_last_post($last_post)
    {
        $this->set_additional_property(self :: PROPERTY_LAST_POST, $last_post);
    }

    function add_last_post($last_post)
    {
        $this->set_last_post($last_post);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = $rdm->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->add_last_post($last_post);
        }
    }

    function recalculate_last_post()
    {
        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name());
        $children = $rdm->retrieve_complex_content_object_items($condition, array(new ObjectTableOrder('add_date', SORT_DESC)), 0, 1);
        $lp = $children->next_result();

        $id = ($lp) ? $lp->get_id() : 0;

        if ($this->get_last_post() != $id)
        {
            $this->set_last_post($id);
            $this->update();

            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
            $wrappers = $rdm->retrieve_complex_content_object_items($condition);

            while ($item = $wrappers->next_result())
            {
                $lo = $rdm->retrieve_content_object($item->get_parent());
                $lo->recalculate_last_post();
            }
        }
    }

    function add_post($posts = 1)
    {
        $this->set_total_posts($this->get_total_posts() + $posts);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = $rdm->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->add_post($posts);
        }
    }

    function remove_post($posts = 1)
    {
        $this->set_total_posts($this->get_total_posts() - $posts);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = $rdm->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->remove_post($posts);
        }
    }

    function is_locked()
    {
    	if($this->get_locked())
    	{
    		return true;
    	}

    	$rdm = RepositoryDataManager :: get_instance();

    	$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $parents = $rdm->retrieve_complex_content_object_items($condition);

        while ($parent = $parents->next_result())
        {
            $content_object = $rdm->retrieve_content_object($parent->get_parent());
            if($content_object->is_locked())
            {
            	return true;
            }
        }

    	return false;
    }

	function invert_locked()
    {
    	$this->set_locked(!$this->get_locked());
    	return $this->update();
    }

}
?>
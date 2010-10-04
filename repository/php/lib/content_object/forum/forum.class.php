<?php
/**
 * $Id: forum.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum
 */
/**
 * This class represents a discussion forum.
 */
class Forum extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_TOTAL_TOPICS = 'total_topics';
    const PROPERTY_TOTAL_POSTS = 'total_posts';
    const PROPERTY_LAST_POST = 'last_post_id';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_locked()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCKED);
    }

    function set_locked($locked)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
    }

    function get_total_topics()
    {
        return $this->get_additional_property(self :: PROPERTY_TOTAL_TOPICS);
    }

    function set_total_topics($total_topics)
    {
        $this->set_additional_property(self :: PROPERTY_TOTAL_TOPICS, $total_topics);
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

        $lp = $rdm->retrieve_last_post($this->get_id());

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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCKED, self :: PROPERTY_TOTAL_TOPICS, self :: PROPERTY_TOTAL_POSTS, self :: PROPERTY_LAST_POST);
    }

    function get_allowed_types()
    {
        return array(Forum :: get_type_name(), ForumTopic :: get_type_name());
    }

    function add_post($posts = 1)
    {
        $this->set_total_posts($this->get_total_posts() + $posts);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

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
        $wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->remove_post($posts);
        }
    }

    function add_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() + $topics);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->add_topic($topics);
        }
    }

    function remove_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() - $topics);
        $this->update();

        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_id());
        $wrappers = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

        while ($item = $wrappers->next_result())
        {
            $lo = $rdm->retrieve_content_object($item->get_parent());
            $lo->remove_topic($topics);
        }
    }

    function delete_links()
    {
    	$success = parent :: delete_links();
    	if($success)
    	{
    		$this->set_total_posts(0);
    		$this->set_total_topics(0);
    		$success = $this->update();
    	}
    	return $success;
    }

    function delete_complex_wrapper($object_id, $link_ids)
    {
    	$rdm = RepositoryDataManager :: get_instance();
    	$failures = 0;

    	foreach($link_ids as $link_id)
    	{
    		$item = $rdm->retrieve_complex_content_object_item($link_id);
    		$object = $rdm->retrieve_content_object($item->get_ref());

    		if($object->get_type() == Forum :: get_type_name())
    		{
    			$this->set_total_topics($this->get_total_topics() - $object->get_total_topics());
    		}

    		$this->set_total_posts($this->get_total_post() - $object->get_total_post());

    		if(!$item->delete())
    		{
    			$failures++;
    			continue;
    		}

    	}

    	if(!$this->update())
    		$failures++;

    	$message = $this->get_result($failures, count($link_ids), 'ComplexContentObjectItemNotDeleted', 'ComplexContentObjectItemsNotDeleted',
    							     'ComplexContentObjectItemDeleted', 'ComplexContentObjectItemsDeleted');

    	return array($message, ($failures > 0));
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
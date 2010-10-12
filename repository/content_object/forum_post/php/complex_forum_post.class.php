<?php
/**
 * @package repository.learningobject
 * @subpackage forum_post
 */

class ComplexForumPost extends ComplexContentObjectItem
{
    const PROPERTY_REPLY_ON_POST = 'reply_on_post_id';

    function get_reply_on_post()
    {
        return $this->get_additional_property(self :: PROPERTY_REPLY_ON_POST);
    }

    function set_reply_on_post($reply_on_post)
    {
        $this->set_additional_property(self :: PROPERTY_REPLY_ON_POST, $reply_on_post);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_REPLY_ON_POST);
    }

    function create()
    {
        parent :: create();

        $parent = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_parent());
        $parent->add_post();
        $parent->add_last_post($this->get_id());
        $parent->recalculate_last_post();

        return true;
    }

    function delete()
    {
        parent :: delete();

        $datamanager = RepositoryDataManager :: get_instance();

        $parent = $datamanager->retrieve_content_object($this->get_parent());
        $parent->remove_post();

        $siblings = $datamanager->count_complex_content_object_items(new EqualityCondition('parent_id', $this->get_parent()));
        if ($siblings == 0)
        {
            $wrappers = $datamanager->retrieve_complex_content_object_items(new EqualityCondition('ref_id', $this->get_parent()));
            while ($wrapper = $wrappers->next_result())
            {
                $wrapper->delete();
            }

            $parent->delete();
        }

        return ture;
        //$parent->recalculate_last_post();
    }
}
?>
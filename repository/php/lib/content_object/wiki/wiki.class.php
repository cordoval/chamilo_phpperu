<?php
/**
 * $Id: wiki.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki
 */
class Wiki extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_LINKS = 'links';
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_allowed_types()
    {
        return array(WikiPage :: get_type_name());
    }

    function get_locked()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCKED);
    }

    function set_locked($locked)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
    }

    function get_links()
    {
        return $this->get_additional_property(self :: PROPERTY_LINKS);
    }

    function set_links($links)
    {
        return $this->set_additional_property(self :: PROPERTY_LINKS, $links);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCKED, self :: PROPERTY_LINKS);
    }

    function get_wiki_pages($return_complex_items = false)
    {
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));

        if ($return_complex_items)
        {
            return $complex_content_objects;
        }

        $wiki_pages = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $wiki_pages[] = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
        }

        return $wiki_pages;
    }

    function get_wiki_pages_by_title(Condition $title_condition)
    {
        $complex_content_object_item_condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name());

        $content_object_conditions = array();
        $content_object_conditions[] = $title_condition;
        $content_object_conditions[] = new SubselectCondition(ContentObject :: PROPERTY_ID, ComplexContentObjectItem :: PROPERTY_REF, ComplexContentObjectItem :: get_table_name(), $complex_content_object_item_condition, ContentObject :: get_table_name());
        $content_object_condition = new AndCondition($content_object_conditions);

        return RepositoryDataManager :: get_instance()->retrieve_content_objects($content_object_condition);
    }
}
?>
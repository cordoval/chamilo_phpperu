<?php
/**
 * $Id: complex_wiki_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki_page
 */

class ComplexWikiPage extends ComplexContentObjectItem
{
    const PROPERTY_IS_HOMEPAGE = 'is_homepage';
    const PROPERTY_IS_LOCKED = 'is_locked';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_IS_HOMEPAGE, self :: PROPERTY_IS_LOCKED);
    }

    function get_is_homepage()
    {
        return $this->get_additional_property(self :: PROPERTY_IS_HOMEPAGE);
    }

    function get_is_locked()
    {
        return $this->get_additional_property(self :: PROPERTY_IS_LOCKED);
    }

    function set_is_homepage($value)
    {
        $this->set_additional_property(self :: PROPERTY_IS_HOMEPAGE, $value);
    }

    function set_is_locked($value)
    {
        $this->set_additional_property(self :: PROPERTY_IS_LOCKED, $value);
    }

    function update()
    {
        if ($this->get_is_homepage())
        {
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_parent(), ComplexContentObjectItem :: get_table_name());
            //$conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, 1);
            //$condition = new AndCondition($conditions);
            

            $rdm = RepositoryDataManager :: get_instance();
            $children = $rdm->retrieve_complex_content_object_items($condition);
            while ($child = $children->next_result())
            {
                if ($child->get_is_homepage())
                {
                    $child->set_is_homepage(0);
                    $child->update();
                    break;
                }
            }
        }
        
        return parent :: update();
    }
}
?>
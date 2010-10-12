<?php
/**
 * $Id: complex_wiki_page_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki_page
 */
require_once dirname(__FILE__) . '/complex_wiki_page.class.php';

class ComplexWikiPageForm extends ComplexContentObjectItemForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $elements = $this->get_elements();
        foreach ($elements as $element)
        {
            $this->addElement($element);
        }
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $elements = $this->get_elements();
        foreach ($elements as $element)
        {
            $this->addElement($element);
        }
    }

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, Translation :: get('IsHomepage'));
        return $elements;
    }

    function setDefaults($defaults = array ())
    {
        $defaults = array_merge($defaults, $this->get_default_values());
        parent :: setDefaults($defaults);
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE] = $cloi->get_is_homepage() ? $cloi->get_is_homepage() : false;
        }
        
        return $defaults;
    }

    function create_complex_content_object_item()
    {
        $values = $this->exportValues();
        $this->create_cloi_from_values($values);
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_is_homepage(empty($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]) ? false : $values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_is_homepage(empty($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]) ? false : $values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_is_homepage(empty($values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]) ? false : $values[ComplexWikiPage :: PROPERTY_IS_HOMEPAGE]);
        return parent :: update_complex_content_object_item();
    }
}

?>
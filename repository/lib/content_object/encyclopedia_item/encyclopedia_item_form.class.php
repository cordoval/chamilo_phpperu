<?php
/**
 * This class describes the form for a EncyclopediaItem object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/encyclopedia_item.class.php';

class EncyclopediaItemForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    private function build_default_form()
    {
        $url = $this->get_path(WEB_PATH) . 'repository/xml_feeds/xml_image_feed.php';
        
        $locale = array();
        $locale['Display'] = Translation :: get('SelectImage');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $this->addElement('image_selecter', EncyclopediaItem :: PROPERTY_IMAGE, Translation :: get('Image'), $url, $locale);
        
        $this->addElement('textarea', EncyclopediaItem :: PROPERTY_TAGS, Translation :: get('Tags'), array('cols' => '70', 'rows' => '5'));
        $this->addRule(EncyclopediaItem :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
        	$defaults[EncyclopediaItem :: PROPERTY_IMAGE] = $content_object->get_image();
        	$defaults[EncyclopediaItem :: PROPERTY_TAGS] = $content_object->get_tags();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new EncyclopediaItem();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: update_content_object();
    }

    private function fill_properties($object)
    {
    	$object->set_image($this->exportValue(EncyclopediaItem :: PROPERTY_IMAGE));
    	$object->set_tags($this->exportValue(EncyclopediaItem :: PROPERTY_TAGS));
    }
}
?>
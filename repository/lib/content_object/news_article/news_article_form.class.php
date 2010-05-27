<?php
/**
 * This class describes the form for a NewsArticle object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/news_article.class.php';

class NewsArticleForm extends ContentObjectForm
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
        $locale['Display'] = Translation :: get('SelectHeaderImage');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $this->addElement('image_selecter', NewsArticle :: PROPERTY_HEADER, Translation :: get('HeaderImage'), $url, $locale);
        
        $this->addElement('textarea', NewsArticle :: PROPERTY_TAGS, Translation :: get('Tags'), array('cols' => '70', 'rows' => '5'));
        $this->addRule(NewsArticle :: PROPERTY_TAGS, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
            $defaults[NewsArticle :: PROPERTY_HEADER] = $content_object->get_header();
            $defaults[NewsArticle :: PROPERTY_TAGS] = $content_object->get_tags();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new NewsArticle();
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
        $object->set_header($this->exportValue(NewsArticle :: PROPERTY_HEADER));
        $object->set_tags($this->exportValue(NewsArticle :: PROPERTY_TAGS));
    }
}

?>
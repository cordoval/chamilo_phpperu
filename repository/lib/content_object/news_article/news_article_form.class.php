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

    function add_image()
    {
        $object = $this->get_content_object();
        
        $is_object_set = ! is_null($object);
        
        $html[] = '<div id="image_container" ' . ($is_object_set ? '' : ' style="display: none;"') . 'class="row">';
        $html[] = '<div class="label">' . Translation :: get('SelectedImage') . '</div>';
        $html[] = '<div class="formw">';
        $html[] = '<div class="element">';
        
        if ($is_object_set)
        {
            $image_id = $object->get_header();
            $image_object = RepositoryDataManager :: get_instance()->retrieve_content_object($image_id);
            
            $dimensions = getimagesize($image_object->get_full_path());            
            $dimensions = ImageManipulation :: rescale($dimensions[ImageManipulation :: DIMENSION_WIDTH], $dimensions[ImageManipulation :: DIMENSION_HEIGHT], 500, 450, ImageManipulation :: SCALE_INSIDE);
            
            $html[] = '<img id="selected_image" style="width: ' . $dimensions[ImageManipulation :: DIMENSION_WIDTH] . 'px; height: ' . $dimensions[ImageManipulation :: DIMENSION_HEIGHT] . 'px;" src="' . $image_object->get_url() . '" />';
        }
        else
        {
            $html[] = '<img id="selected_image" />';
        
        }
        
        $html[] = '<div class="clear"></div>';
        $html[] = '<button id="change_image" class="negative delete">' . htmlentities(Translation :: get('SelectAnotherImage')) . '</button>';
        
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $this->addElement('html', implode("\n", $html));
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
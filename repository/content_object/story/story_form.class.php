<?php
/**
 * This class describes the form for a Story object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/story.class.php';

class StoryForm extends ContentObjectForm
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
        $this->add_warning_message('header_message', null, Translation :: get('HeaderRequiredForFeaturedItems'));
        
        $locale = array();
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $locale['Display'] = Translation :: get('SelectHeaderImage');
        
        $url = $this->get_path(WEB_PATH) . 'repository/xml_feeds/xml_image_feed.php';
        $extract = $this->addElement('image_selecter', Story :: ATTACHMENT_HEADER, Translation :: get('HeaderImage'), $url, $locale);
        $extract->setHeight('100');
    }

    function setDefaults($defaults = array ())
    {
        $content_object = $this->get_content_object();
        if (isset($content_object))
        {
            $defaults[Story :: ATTACHMENT_HEADER] = $content_object->get_header(true);
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new Story();
        parent :: set_content_object($object);
        $object = parent :: create_content_object();
        $this->process_attachments($object);
        return $object;
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        parent :: set_content_object($object);
        parent :: update_content_object();
        $this->process_attachments($object);
        return true;
    }

    private function process_attachments(ContentObject $object)
    {
        $object->set_headers($this->exportValue(Story :: ATTACHMENT_HEADER));
    }
}
?>
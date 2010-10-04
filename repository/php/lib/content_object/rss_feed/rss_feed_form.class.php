<?php
/**
 * $Id: rss_feed_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rss_feed
 */
require_once dirname(__FILE__) . '/rss_feed.class.php';
class RssFeedForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(RssFeed :: PROPERTY_URL, Translation :: get('URL'), true, ' size="100" style="width: 100%;"');
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(RssFeed :: PROPERTY_URL, Translation :: get('URL'), true, ' size="100" style="width: 100%;"');
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[RssFeed :: PROPERTY_URL] = $lo->get_url();
        }
        else
        {
            $defaults[RssFeed :: PROPERTY_URL] = 'http://';
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $content_object = new RssFeed();
        $content_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
        $this->set_content_object($content_object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $content_object = $this->get_content_object();
        $content_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
        return parent :: update_content_object();
    }
}
?>
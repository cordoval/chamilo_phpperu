<?php
/**
 * $Id: blog_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog
 *
 */
require_once dirname(__FILE__) . '/blog.class.php';
/**
 * This class represents a form to create or update blogs
 */
class BlogForm extends ContentObjectForm
{
    // Inherited
    function create_content_object()
    {
        $object = new Blog();
        $object->set_blog_layout($this->exportValue(Blog :: PROPERTY_BLOG_LAYOUT));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
    
    function update_content_object()
    {
    	$object = $this->get_content_object();
    	$object->set_blog_layout($this->exportValue(Blog :: PROPERTY_BLOG_LAYOUT));
    	return parent :: update_content_object();
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('select', Blog :: PROPERTY_BLOG_LAYOUT, Translation :: get('BlogLayout'), Blog :: get_available_blog_layouts());
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('select', Blog :: PROPERTY_BLOG_LAYOUT, Translation :: get('BlogLayout'), Blog :: get_available_blog_layouts());
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $blog = $this->get_content_object();
        if (isset($blog))
        {
            $defaults[Blog :: PROPERTY_BLOG_LAYOUT] = $blog->get_blog_layout();
        }
        parent :: setDefaults($defaults);
    }

}
?>
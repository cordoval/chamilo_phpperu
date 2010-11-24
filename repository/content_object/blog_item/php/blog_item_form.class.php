<?php
namespace repository\content_object\blog_item;

use repository\ContentObjectForm;

/**
 * $Id: blog_item_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog_item
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/blog_item.class.php';
/**
 * This class represents a form to create or update blog_items
 */
class BlogItemForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new BlogItem();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
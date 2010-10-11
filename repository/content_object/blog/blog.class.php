<?php
/**
 * $Id: blog.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog
 *
 */
/**
 * This class represents an blog
 */
class Blog extends ContentObject implements ComplexContentObjectSupport
{
	const CLASS_NAME = __CLASS__;
	const PROPERTY_BLOG_LAYOUT = 'blog_layout';

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = BlogItem :: get_type_name();
        return $allowed_types;
    }
    
    function get_blog_layout()
    {
        return $this->get_additional_property(self :: PROPERTY_BLOG_LAYOUT);
    }

    function set_blog_layout($blog_layout)
    {
        return $this->set_additional_property(self :: PROPERTY_BLOG_LAYOUT, $blog_layout);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_BLOG_LAYOUT);
    }
    
    static function get_available_blog_layouts()
    {
    	$blog_layouts = array();
    	
    	$dir = dirname(__FILE__) . '/display/component/viewer/blog_layout/';
    	$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);
    	foreach($files as $file)
    	{
    		$file = basename($file);
    		if(substr($file, 0, 1) == '.')
    		{
    			continue;
    		}
    		
    		$type = substr($file, 0, -22);
    		$blog_layouts[$type] = Translation :: get(Utilities :: underscores_to_camelcase($type) . 'BlogLayout');
    	}
    	
    	return $blog_layouts;
    	
    }

}
?>
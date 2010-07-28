<?php

/**
 * Abstract class to define a blog layout so users are able to define new blog layouts and choose between them in the local settings
 * @author Sven Vanpoucke
 */
abstract class BlogLayout
{
	/**
	 * The parent on which this blog layout is rendering
	 */
	private $parent;
	
	/**
	 * The blog which needs to be rendered
	 * @var Blog
	 */
	private $blog;
	
	/**
	 * Constructor
	 * @param $parent
	 * @param Blog $blog
	 */
	function BlogLayout($parent, Blog $blog)
	{
		$this->parent = $parent;
		$this->blog = $blog;
	}
	
	/**
	 * Factory
	 * @param $parent
	 * @param Blog $blog
	 */
	function factory($parent, Blog $blog)
	{
		$type = $blog->get_blog_layout();

		$file = dirname(__FILE__) . '/blog_layout/' . $type . '_blog_layout.class.php';
		if(!file_exists($file))
		{
			throw new Exception(Translation :: get('BlogLayoutNotExists', array('BLOGLAYOUT' => $type)));
		}
		
		require_once $file; 
		
		$class = Utilities :: underscores_to_camelcase($type) . 'BlogLayout';
		return new $class($parent, $blog);
	}
	
	// Getters and setters
	
	function get_blog()
	{
		return $this->blog;
	}
	
	function set_blog(Blog $blog)
	{
		$this->blog = $blog;
	}
	
	function get_parent()
	{
		return $this->parent;
	}
	
	function set_parent($parent)
	{
		$this->parent = $parent;
	}
	
	// Render methods
	
	function render()
	{
		echo $this->as_html();
	}

	function as_html()
	{
		$html = array();
		
		$complex_blog_items = $this->retrieve_complex_blog_items();
		while($complex_blog_item = $complex_blog_items->next_result())
		{
			$html[] = $this->display_blog_item($complex_blog_item);
		}
		
		return implode("\n", $html);
	}
	
	/**
	 * Displays a given blog item
	 * @param ComplexBlogItem $complex_blog_item
	 */
	abstract function display_blog_item(ComplexBlogItem $complex_blog_item);
	
	// Helper methods
	
	/**
	 * Returns the actions for the blog item 
	 * @param ComplexBlogItem $complex_blog_item
	 */
	function get_blog_item_actions($complex_blog_item)
    {
    	$toolbar = new Toolbar();
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->get_parent()->get_complex_content_object_item_update_url($complex_blog_item),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->get_parent()->get_complex_content_object_item_delete_url($complex_blog_item),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
			));
        }
        
        return $toolbar->as_html();
    }
    
    /**
     * Retrieves the children of the current blog by date
     */
    function retrieve_complex_blog_items()
    {
   		$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_blog()->get_id(), ComplexContentObjectItem :: get_table_name());
    	return RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, null, null, new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_ADD_DATE, SORT_DESC));
    }
	
}
<?php

class PageStorage
{
	private $pages; 
		
	public function Pagestorage($controller)
	{
		$this->pages = array();	
	}
	
	
	public function add_page($page)
	{
		if(!is_null($page))
		{
			array_push($this->pages, $page);
		}
	}
	
	/*
	 * Delete an Pages from the pagestorage
	 */
	public function delete_page($Page)
	{
		if(!is_null($pages))
		{
			for($i=0; $i<count($this->pages);$i++)
			{
				if($this->pages[$i]===$pages)
    				unset($this->pages[$i]);
			}	
		}	
	}
	
	/*
	 * Retrieve a certain Pages from the pagestorage
	 */
	public function retrieve_page($page)
	{
		$object = null;
		if(!is_null($page))
		{
			foreach ($this->pages as $value) 
			{
    			if($value->get_name() == $page)
    				$object = $value;
			}		
		}		
		return $object;
	}
	
	/*
	 * Get the array of the pages that were addded to the form
	 */
	public function get_pages()
	{
		return $this->pages;
	}
}
?>
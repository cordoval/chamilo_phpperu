<?php
abstract class TreeMenuDataProvider
{
	private $selected_tree_menu_item;
	private $url;
	
	function TreeMenuDataProvider($url, $type = null)
	{
		$this->set_url($url);
	}
	
	public function get_selected_tree_menu_item()
	{
		return $this->selected_item;
	}
	
	public function set_selected_tree_menu_item($selected_tree_menu_item)
	{
		$this->selected_tree_menu_item = $selected_tree_menu_item;
	}
	
	public function get_url()
	{
		return $this->url;
	}
	
	public function set_url($url)
	{
		$this->url = $url;
	}
	
	public function format_url($id)
	{
		return $this->get_url() . '&' . $this->get_id_param() . '=' . $id; 
	}
	
	abstract function get_tree_menu_data();
	
	abstract function get_id_param();
}
?>
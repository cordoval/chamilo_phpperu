<?php
/*
 * Class to create a group of 1 or more elements on a form
 */
class Grouping extends Container
{	
	private $append; //boolean value to indicate if name of the group and name of elements have to be appended
	/*
	 * Constructor for the Group class
	 */
	public function Grouping($name, $label, $append)
	{
		parent::Container($name, $label);
		$this->append = $append;
	}	
	
	/*
	 * This function returns the HTML of the elements that are grouped
	 */
	public function render()
	{
		$text = '';
		foreach($this->get_elements() as $i)
		{
			if($this->append == true)
			{
				$name = $this->name .'['. $i->get_name() .']';
				$i->set_name($name);
			}	
			$text .= $i->render();								
	}						
		return $text . $this->get_errors();
	}		
}
?>
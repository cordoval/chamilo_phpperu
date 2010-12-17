<?php 
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Select.class.php';

class Timepicker extends Grouping 
{
	protected $options;
	
	public function Timepicker($elementName, $elementLabel, $options = array())
	{
		parent::Grouping($elementName, $elementLabel, false);
		$this->create_elements();
		$this->options = $options;
	}	
	
	public function create_elements()
	{
		$hours = array();
		for($i = 0; $i <= 23; ++$i)
		{
			if($i<10)
				$i = "0" . $i;
			$hours[$i]=$i;
		}
		$hour = new Select($this->form, 'Hour', 'Hour', $hours);
		$this->elements[] = $hour;
		
		$label = new Label($this->form, ' h ');
		$this->elements[] = $label;
		
		$minutes = array();
		for($i = 0; $i <= 59; ++$i)
		{
			if($i<10)
				$i = "0" . $i;
			$minutes[$i]=$i;
		}
		$minute = new Select($this->form, 'Minute', 'Minute', $minutes);
		$this->elements[] = $minute;

		$current = date('H');
		$hour->set_value($current);
		$current = date('i');
		$minute->set_value($current);
	}	
	
	public function get_value()
	{
		if(!empty($_POST['Hour']) && !empty($_POST['Minute']))
			$timestamp = mktime($_POST['Hour'], $_POST['Minute'], 0, 0 , 0, 0);
		return $timestamp;
	}
}
?>
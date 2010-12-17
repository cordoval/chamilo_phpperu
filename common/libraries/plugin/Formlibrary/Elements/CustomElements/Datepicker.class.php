<?php 
require_once ('plugin/FormLibrary/Elements/Select.class.php');

class Datepicker extends Grouping 
{
	protected $options;
	
	public function Datepicker($elementName, $elementLabel, $options = array())
	{
		parent::Grouping($elementName, $elementLabel, false);
		$this->create_elements();
		$this->options = $options;
	}	
	
	public function create_elements()
	{
		$days = array();
		for($i = 1; $i <= 31; ++$i)
		{
			if($i<10)
				$i = "0" . $i;
			$days[$i]=$i;
		}
		$day = new Select($this->form, "Day", 'Day', $days);
		$this->elements[] = $day;
		
		$months = array();
		for($i = 1; $i <= 12; ++$i)
		{
			if($i<10)
				$i = "0" . $i;
			$months[$i]=$i;
		}
		$month = new Select($this->form, "Month", 'Month', $months);
		$this->elements[] = $month;
		
		$years = array();
		$year = date("Y");		
		for($i = 1; $i <= 50; ++$i)
		{
			if($i<10)
				$i = "0" . $i;
			$years[$year]=$year;
			++$year;
		}
		$year = new Select($this->form, "Year", 'Year', $years);
		$this->elements[] = $year;
		
		$current = getdate(time());
		$day->set_value($current[mday]);
		$month->set_value($current[mon]);
		$year->set_value($current[year]);			
	}	
	
	public function get_value()
	{
		if(!empty($_POST['Day']) && !empty($_POST['Month']) && !empty($_POST['Year']))
			$timestamp = mktime(0, 0, 0, $_POST['Day'] , $_POST['Month'], $_POST['Year']);		
		return $timestamp;
	}
}
?>
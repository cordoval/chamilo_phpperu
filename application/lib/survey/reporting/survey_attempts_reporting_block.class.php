<?php
class SurveyAttemptsReportingBlock extends SurveyReportingBlock
{
	public function count_data()
	{
		return 0;	
	}	
	
	public function retrieve_date()
	{
		$base_path = (WebApplication :: is_application($this->get_application()) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        
        $file = $base_path . $this->get_application() . '/reporting/reporting_' . $this->get_application() . '.class.php';
        require_once $file;
        $this->data = call_user_func('Reporting' . $this->get_application() . '::' . $this->get_function(), $this->get_function_parameters());
	}
	
}
?>
<?php
abstract class EvaluationsReportingBlock extends ReportingBlock
{
	public function get_data_manager()
	{
		return GradebookDataManager::get_instance();
	}
}
?>
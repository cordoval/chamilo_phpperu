<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DatetimeUtilities;
use common\libraries\EqualityCondition;

use repository\RepositoryDataManager;
use user\UserDataManager;

use reporting\ReportingData;
use reporting\ReportingFormatter;

require_once WebApplication :: get_application_class_path('gradebook') . 'reporting/evaluations_reporting_block.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/gradebook_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_format/evaluation_format.class.php';


class PublicationEvaluationsReportingBlock extends EvaluationsReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('EvaluationDate'),Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'), Translation :: get('Comment')));
		$application = Request :: get(GradebookManager :: PARAM_PUBLICATION_APP);
		$publication_id = Request :: get(GradebookManager :: PARAM_PUBLICATION_ID);
		$type = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
		if($type == 'internal')
		{
			$reporting_data->set_rows(array(Translation :: get('EvaluationDate'),Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'), Translation :: get('Comment')));
			$data = GradebookManager :: retrieve_all_evaluations_on_internal_publication($application, $publication_id);
			$internal_item = EvaluationManager :: retrieve_internal_item_by_publication($application, $publication_id);
			if($internal_item->get_calculated() == 0)
			{
				while($evaluation = $data->next_result())
				{
					$optional_properties = $evaluation->get_optional_properties();
					$format = GradebookManager :: retrieve_evaluation_format($evaluation->get_format_id());
					$evaluation_format = EvaluationFormat :: factory($format->get_title());
					$evaluation_format->set_score($optional_properties['score']);

					$reporting_data->add_category($evaluation->get_id());
		            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('EvaluationDate'), DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $evaluation->get_evaluation_date()));
		            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('User', null, 'user'), $optional_properties['user']);
					$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Evaluator'), $optional_properties['evaluator']);
					$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Score'), $evaluation_format->get_formatted_score());
					$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Comment'), $optional_properties['comment']);
					$reporting_data->hide_categories();
				}
			}
			else
			{
				$application_manager = WebApplication :: factory($application);
	        	$attributes = $application_manager->get_content_object_publication_attribute($publication_id);
	        	$rdm = RepositoryDataManager :: get_instance();
				$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());

				if(!($content_object->get_type() == $application))
				{
					$tool = $content_object->get_type();
				}
				$udm = UserDataManager :: get_instance();
				$connector = GradebookConnector :: factory($application, $tool);
				$user = $connector->get_tracker_user($publication_id);
				$date = $connector->get_tracker_date($publication_id);
				$score = $connector->get_tracker_score($publication_id);
				$publisher = $udm->retrieve_user($content_object->get_owner_id())->get_fullname();
				$content_date = ($content_object->get_modification_date());
				if($user)
				{
					for($i=0;$i<count($user);$i++)
					{
						$score_translation = 'Score';
						$username = $udm->retrieve_user($user[$i])->get_fullname();
						if(!$date[$i])
						{
							$date[$i] = $content_date;
						}
						$reporting_data->add_category($date[$i]);
			            $reporting_data->add_data_category_row($date[$i], Translation :: get('EvaluationDate'), DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $date[$i]));
			            $reporting_data->add_data_category_row($date[$i], Translation :: get('User', null, 'user'), $username);
						$reporting_data->add_data_category_row($date[$i], Translation :: get('Evaluator'), $publisher);
						$reporting_data->add_data_category_row($date[$i], Translation :: get($score_translation), $score[$i] . '%');
						$reporting_data->add_data_category_row($date[$i], Translation :: get('Comment'), 'automatic generated result');
						$reporting_data->hide_categories();
					}
				}
			}
		}
		else
		{
			//$reporting_data->set_rows(array(Translation :: get('EvaluationDate'), Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'), Translation :: get('Comment')));
			require_once dirname(__FILE__) . '/../../external_item.class.php';
			$condition = new EqualityCondition(ExternalItem :: PROPERTY_ID, $publication_id, ExternalItem :: get_table_name());
			$data = GradebookManager :: retrieve_all_evaluations_on_external_publication($condition);
			$udm = UserDataManager :: get_instance();
			while($evaluation = $data->next_result())
			{
				$evaluator_name = $udm->retrieve_user($evaluation->get_evaluator_id())->get_fullname();
				$optional_properties = $evaluation->get_optional_properties();
				$format = GradebookManager :: retrieve_evaluation_format($evaluation->get_format_id());
				$evaluation_format = EvaluationFormat :: factory($format->get_title());
				$evaluation_format->set_score($optional_properties['score']);
				$reporting_data->add_category($evaluation->get_id());
	            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('EvaluationDate'), DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $evaluation->get_evaluation_date()));
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Evaluator'), $evaluator_name);
	            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('User', null, 'user'), $optional_properties['evaluator']);
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Score'), $evaluation_format->get_formatted_score());
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Comment'), $optional_properties['comment']);
				$reporting_data->hide_categories();
			}
		}
		return $reporting_data;
	}

	public function retrieve_data()
	{
		return $this->count_data();
	}

	function get_application()
	{
		return GradebookManager::APPLICATION_NAME;
	}

	public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter ::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>
<?php
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';
class EvaluationForm extends FormValidator
{
	private $auto_generate_evaluation_applications = array("assessment", "weblcms");
	
	const PROPERTY_FORMAT_LIST = 'format_list';
	
	static function build_evaluation_question($form, $choose_format = false)
	{
		$form->addElement('category', Translation :: get('EvaluationProperties'));
		
		if($choose_format)
		{
			
		}
		else
		{
			
			$formats = GradebookDatamanager :: get_instance()->retrieve_all_active_evaluation_formats();
			while($format = $formats->next_result())
			{
				$formats_array[$format->get_id()] = $format->get_title();
			}
       			$form->addElement('checkbox', 'evaluation' , Translation :: get('CreateEvaluation'), null, 'onclick="javascript:showElement(\'' . self :: PROPERTY_FORMAT_LIST . '\')"');
       			$form->add_element_hider('script_block');
       			$form->add_element_hider('begin', self :: PROPERTY_FORMAT_LIST);
        		$form->addElement('select', self :: PROPERTY_FORMAT_LIST ,Translation :: get('EvaluationFormat'), $formats_array);
        		$form->add_element_hider('end', self :: PROPERTY_FORMAT_LIST);
		}
	$form->addElement('category');
	}
}
/* 	if(WebApplication :: is_active('gradebook'))
        {
        	require_once dirname (__FILE__) . '../../../../lib/gradebook/forms/evaluation_form.class.php';
        	EvaluationForm :: build_evaluation_question($form);
        }*/

?>
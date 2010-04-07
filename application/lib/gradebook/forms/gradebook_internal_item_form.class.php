<?php
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';
require_once dirname (__FILE__) . '/../internal_item.class.php';
class GradebookInternalItemForm extends FormValidator
{

    function GradebookInternalItemForm()
    {
    }
    
	function build_evaluation_question($form)
	{
		$form->addElement('checkbox', 'evaluation' , Translation :: get('CreateEvaluation'));
//		if(!$choose_format)
//		{
//		}
//		else
//		{
//			$formats = GradebookDatamanager :: get_instance()->retrieve_all_active_evaluation_formats();
//			while($format = $formats->next_result())
//			{
//				$formats_array[$format->get_id()] = $format->get_title();
//			}
//       			$form->addElement('checkbox', 'evaluation' , Translation :: get('CreateEvaluation'), null, 'onclick="javascript:showElement(\'' . self :: PROPERTY_FORMAT_LIST . '\')"');
//       			$form->add_element_hider('script_block');
//       			$form->add_element_hider('begin', self :: PROPERTY_FORMAT_LIST);
//        		$form->addElement('select', self :: PROPERTY_FORMAT_LIST ,Translation :: get('EvaluationFormat'), $formats_array);
//        		$form->add_element_hider('end', self :: PROPERTY_FORMAT_LIST);
//		}
	}
	
	function create_internal_item($publication_id, $calculated)
	{
		$internal_item = new InternalItem();
		$internal_item->set_application(Request :: get('application'));
		$internal_item->set_publication_id($publication_id);
		$internal_item->set_calculated($calculated);
		$internal_item->create();
	}
}
?>
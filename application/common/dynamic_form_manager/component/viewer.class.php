<?php
/**
 * $Id: viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */
require_once 'HTML/Table.php';
require_once (dirname(__FILE__) . '/../dynamic_form_element.class.php');
require_once (dirname(__FILE__) . '/../dynamic_form_element_option.class.php');
require_once (dirname(__FILE__) . '/../dynamic_form_element_value.class.php');

class DynamicFormManagerViewerComponent extends DynamicFormManager
{
    private $table;
    
	function run()
    {
    	$values = $this->get_form_values();
    	
    	$this->build_table_header();
    	
    	$counter = 1;
    	
    	while($value = $values->next_result())
    	{
    		$this->build_table_element($value, $counter);
    		$counter++;
    	}
    	
    	$this->build_table_footer();
    	
    	if($values->size() != 0)
    		echo $this->table->toHtml();
    }
    
    function get_form_values()
    {
    	$form = $this->get_form()->get_id();
    	$target_user = $this->get_target_user_id();
    	
    	$subcondition = new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID, $form);
    	$conditions[] = new SubselectCondition(DynamicFormElementValue :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, DynamicFormElement :: PROPERTY_ID, 
    										'admin_' . DynamicFormElement :: get_table_name(), $subcondition);
    	$conditions[] = new EqualityCondition(DynamicFormElementValue :: PROPERTY_USER_ID, $target_user);
    	$condition = new AndCondition($conditions);
    	
    	return AdminDataManager :: get_instance()->retrieve_dynamic_form_element_values($condition);
    }
    
    function build_table_header()
    {
		$table = $this->table = new Html_Table(array('class' => 'data_table'));
		$table->setHeaderContents(0, 0, $this->get_dynamic_form_title());
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
        $table->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
    }
    
    function build_table_footer()
    {
    	
    }
    
    function build_table_element($element_value, $row)
    {
    	$table = $this->table;
    	
    	$condition = new EqualityCondition(DynamicFormElement :: PROPERTY_ID, $element_value->get_dynamic_form_element_id());
    	$element = AdminDataManager :: get_instance()->retrieve_dynamic_form_elements($condition)->next_result();
    	
    	$table->setCellContents($row, 0, $element->get_name());
        $table->setCellAttributes($row, 0, array('style' => 'width: 150px;'));
    	
    	$table->setCellContents($row, 1, $element_value->get_value());
    }
}
?>
<?php
/**
 * $Id: MaxValue.php 176 2009-11-12 13:25:10Z vanpouckesven $
 * @package common.html.formvalidator.Rule
 */
require_once ('HTML/QuickForm/Rule.php');

class HTML_QuickForm_Rule_MaxValue extends HTML_QuickForm_Rule
{

    function validate($width, $values = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $values[HomeColumn :: PROPERTY_ROW]);
        $conditions[] = new NotCondition(new EqualityCondition(HomeColumn :: PROPERTY_ID, $values[HomeColumn :: PROPERTY_ID]));
        $condition = new AndCondition($conditions);
        
        $columns = HomeDataManager :: get_instance()->retrieve_home_columns($condition);
        
        $total_width = 0;
        
        while ($column = $columns->next_result())
        {
            $total_width += $column->get_width();
        }
        
        $columns_amount = $columns->size();
        
        $total_width += $columns_amount;
        $max_width = 100 - $total_width;
        
        if ($width > $max_width || $max_width <= 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
?>
<?php
/**
 * $Id: condition_translator.class.php 229 2009-11-16 09:02:34Z scaramanga $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
require_once dirname(__FILE__) . '/equality_condition.class.php';
require_once dirname(__FILE__) . '/inequality_condition.class.php';
require_once dirname(__FILE__) . '/pattern_match_condition.class.php';
require_once dirname(__FILE__) . '/aggregate_condition.class.php';
require_once dirname(__FILE__) . '/and_condition.class.php';
require_once dirname(__FILE__) . '/or_condition.class.php';
require_once dirname(__FILE__) . '/not_condition.class.php';
require_once dirname(__FILE__) . '/in_condition.class.php';
require_once dirname(__FILE__) . '/like_condition.class.php';
require_once dirname(__FILE__) . '/subselect_condition.class.php';

class ConditionTranslator
{
    private $data_manager;
    private $storage_unit;

    function ConditionTranslator($data_manager, $storage_unit = null)
    {
        $this->data_manager = $data_manager;
        $this->storage_unit = $storage_unit;
    }

    function translate($condition)
    {
        if ($condition instanceof AggregateCondition)
        {
            $string = $this->translate_aggregate_condition($condition);
        }
        elseif ($condition instanceof InCondition)
        {
            $string = $this->translate_in_condition($condition);
        }
        elseif ($condition instanceof SubselectCondition)
        {
            $string = $this->translate_subselect_condition($condition);
        }
        elseif ($condition instanceof Condition)
        {
            $string = $this->translate_simple_condition($condition);
        }
        else
        {
            //			dump($condition);
            die('Need a Condition instance');
        }
        
        return $string;
    }

    /**
     * Translates an aggregate condition to a SQL WHERE clause.
     * @param AggregateCondition $condition The AggregateCondition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     * prefix learning
     * object properties
     * to avoid collisions.
     * @return string The WHERE clause.
     */
    /**
     * Translates an aggregate condition to a SQL WHERE clause.
     * @param AggregateCondition $condition The AggregateCondition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     * prefix learning
     * object properties
     * to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_aggregate_condition($aggregate_condition)
    {
        $string = '';
        
        if ($aggregate_condition instanceof AndCondition || $aggregate_condition instanceof OrCondition)
        {
            $cond = array();
            $count = 0;
           
            foreach ($aggregate_condition->get_conditions() as $key => $condition)
            {
                $count ++;
                $translation = $this->translate($condition);
                
                if (! empty($translation))
                {
                    $string .= $translation;
                    
                    if ($count < count($aggregate_condition->get_conditions()))
                    {
                        $conditions = $aggregate_condition->get_conditions();
                        
                        $next_condition = $conditions[$key + 1];
                        
                        if (! ($next_condition instanceof InCondition && $this->translate($next_condition) == ''))
                        {
                        	$string .= $aggregate_condition->get_operator();
                        }
                    }
                }
            }
            
            if (! empty($string))
            {
                $string = '(' . $string . ')';
            }
        }
        elseif ($aggregate_condition instanceof NotCondition)
        {
            $string .= 'NOT (';
            $string .= $this->translate($aggregate_condition->get_condition());
            $string .= $this->strings[] = ')';
        }
        else
        {
            die('Cannot translate aggregate condition');
        }
        
        return $string;
    }

    /**
     * Translates an in condition to a SQL WHERE clause.
     * @param InCondition $condition The InCondition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     * prefix learning
     * object properties
     * to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_in_condition($condition)
    {
        $storage_unit = $this->storage_unit;
        $condition_storage_unit = $condition->get_storage_unit();
        
        if (! is_null($condition_storage_unit))
        {
            $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
        }
        
        if ($condition instanceof InCondition)
        {
            $name = $condition->get_name();
            $values = $condition->get_values();
            
            if (! is_array($values))
            {
                $values = array($values);
            }
            
            if (count($values) > 0)
            {
                $where_clause = $this->data_manager->escape_column_name($name, $storage_unit) . ' IN (';
                
                $placeholders = array();
                foreach ($values as $value)
                {
                    $placeholders[] = $this->data_manager->quote($value);
                }
                
                $where_clause .= implode(',', $placeholders) . ')';
                return $where_clause;
            }
            else
            {
                return '';
            }
        }
        else
        {
            die('Cannot translate in condition');
        }
    }

    function translate_subselect_condition($condition)
    {
        if ($condition instanceof SubselectCondition)
        {
            $storage_unit = $this->storage_unit;
            
            $name = $condition->get_name();
            $value = $condition->get_value();
            $table = $condition->get_storage_unit_value();
            $name_table = $condition->get_storage_unit_name();
            //$etable = $this->data_manager->escape_table_name($table);
            $etable = $table;
            $sub_condition = $condition->get_condition();
            
            $alias = $this->data_manager->get_alias($table);
            if ($name_table)
            {
                $alias_name = $this->data_manager->get_alias($name_table);
            }
            
            $this->storage_unit = $alias;
            $string = $this->data_manager->escape_column_name($name, $alias_name) . ' IN ( SELECT ' . $this->data_manager->escape_column_name($value, $alias) . ' FROM ' . $etable . ' AS ' . $alias;
            
            if ($sub_condition)
            {
                $string .= ' WHERE ';
                $string .= $this->translate($sub_condition);
            }
            
            $string .= ')';
            $this->storage_unit = $storage_unit;
        }
        else
        {
            die('Cannot translate in condition');
        }
        
        return $string;
    }

    /**
     * Translates a simple condition to a SQL WHERE clause.
     * @param Condition $condition The Condition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     * prefix learning
     * object properties
     * to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_simple_condition($condition)
    {
        $storage_unit = $this->storage_unit;
        $data_manager = $this->data_manager;
        
        $name = $condition->get_name();
        $condition_storage_unit = $condition->get_storage_unit();
        
        if (! is_null($condition_storage_unit))
        {
            $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
        }
        
        if ($condition instanceof EqualityCondition)
        {
            $value = $condition->get_value();
            
            if ($data_manager->is_date_column($name))
            {
                $value = self :: to_db_date($value);
            }
            
            if (is_null($value))
            {
                return $this->data_manager->escape_column_name($name, $storage_unit) . ' IS NULL';
            }
            
            return $this->data_manager->escape_column_name($name, $storage_unit) . ' = ' . $this->data_manager->quote($value);
        }
        elseif ($condition instanceof InequalityCondition)
        {
            $value = $condition->get_value();
            
            if ($data_manager->is_date_column($name))
            {
                $value = self :: to_db_date($value);
            }
            
            switch ($condition->get_operator())
            {
                case InequalityCondition :: GREATER_THAN :
                    $operator = '>';
                    break;
                case InequalityCondition :: GREATER_THAN_OR_EQUAL :
                    $operator = '>=';
                    break;
                case InequalityCondition :: LESS_THAN :
                    $operator = '<';
                    break;
                case InequalityCondition :: LESS_THAN_OR_EQUAL :
                    $operator = '<=';
                    break;
                default :
                    die('Unknown operator for inequality condition');
            }
            
            return $this->data_manager->escape_column_name($name, $storage_unit) . ' ' . $operator . ' ' . $this->data_manager->quote($value);
        }
        elseif ($condition instanceof PatternMatchCondition)
        {
            return $this->data_manager->escape_column_name($condition->get_name(), $storage_unit) . ' LIKE ' . $this->data_manager->quote($this->translate_search_string($condition->get_pattern()));
        }
        else
        {
            return $condition; //die('Cannot translate condition');
        }
    }

    /**
     * Translates a string with wildcard characters "?" (single character)
     * and "*" (any character sequence) to a SQL pattern for use in a LIKE
     * condition. Should be suitable for any SQL flavor.
     * @param string $string The string that contains wildcard characters.
     * @return string The escaped string.
     */
    static function translate_search_string($string)
    {
        /*
		 ======================================================================
		 * A brief explanation of these regexps:
		 * - The first one escapes SQL wildcard characters, thus prefixing
		 *   %, ', \ and _ with a backslash.
		 * - The second one replaces asterisks that are not prefixed with a
		 *   backslash (which escapes them) with the SQL equivalent, namely a
		 *   percent sign.
		 * - The third one is similar to the second: it replaces question
		 *   marks that are not escaped with the SQL equivalent _.
		 ======================================================================
		 */
        return preg_replace(array('/([%\'\\\\_])/e', '/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array("'\\\\\\\\' . '\\1'", '%', '_'), $string);
    }

    function render_query($condition)
    {
        return ' WHERE ' . $this->translate($condition);
    }

    /**
     * Converts a UNIX timestamp (as returned by time()) to a datetime string
     * for use in SQL queries.
     * @param int $date The date as a UNIX timestamp.
     * @return string The date in datetime format.
     */
    static function to_db_date($date)
    {
        if (isset($date))
        {
            return date('Y-m-d H:i:s', $date);
        }
        return null;
    }
}
?>
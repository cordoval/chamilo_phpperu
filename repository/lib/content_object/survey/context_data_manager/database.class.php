<?php
/**
 * $Id: database.class.php 234 2009-11-16 11:34:07Z vanpouckesven $
 * @package repository.lib.data_manager
 */
//require_once dirname(__FILE__) . '/database/database_content_object_result_set.class.php';
//require_once dirname(__FILE__) . '/database/database_complex_content_object_item_result_set.class.php';
//require_once dirname(__FILE__) . '/../category_manager/repository_category.class.php';
//
//require_once 'MDB2.php';


/**
==============================================================================
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Tim De Pauw
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @author Dieter De Neef
==============================================================================
 */

require_once (dirname(__FILE__) . '/../context/survey_default_context/survey_default_context.class.php');
require_once (dirname(__FILE__) . '/../context/survey_student_context/survey_student_context.class.php');

class DatabaseSurveyContextDataManager extends SurveyContextDataManager
{

    //     Inherited.
    function initialize()
    {
        PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array(get_class(), 'handle_error'));
	    $this->database = new Database();
        $this->database->set_prefix('repository_');
    
    }

    function query($query)
    {
        return $this->database->query($query);
    }

    function retrieve_survey_contexts($condition = null, $offset = null, $count = null, $order_property = null)
    {
        ;
    }

    function retrieve_survey_context_by_id($id, $type)
    {
        if (! isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID || $id == 0)
        {
            return null;
        }
        
        if (is_null($type))
        {
            $type = $this->determine_survey_context_type($id);
        }
        
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
        //context is always extended because SurveyContext is an abstact class        
        //        if ($this->is_extended_type($type))
        //        {
        $survey_context_alias = $this->database->get_alias(SurveyContext :: get_table_name());
        
        //dump($condition);
        

        $query = 'SELECT * FROM ' . $this->database->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $survey_context_alias;
        $query .= ' JOIN ' . $this->database->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $this->database->escape_column_name(SurveyContext :: PROPERTY_ID, $survey_context_alias) . '=' . $this->database->escape_column_name(SurveyContext :: PROPERTY_ID, self :: ALIAS_TYPE_TABLE);
        
        //dump($query);
        

        $record = $this->database->retrieve_row($query, SurveyContext :: get_table_name(), $condition);
        //        }
        //        else
        //        {
        //            $record = $this->database->retrieve_record(SurveyContext :: get_table_name(), $condition);
        //        }
        

        return self :: record_to_survey_context($record, isset($type));
    }

    function delete_survey_context($context)
    {
        
    	if ($context->get_additional_properties())
        {
            $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
            $this->database->delete_objects(Utilities :: camelcase_to_underscores(get_class($context)), $condition);
        }
        
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
        $this->database->delete_objects(SurveyContext :: get_table_name(), $condition);
    }

    function update_survey_context($survey_context)
    {
        ;
    }

    function create_survey_context($context)
    {
        $props = array();
        foreach ($context->get_default_properties() as $key => $value)
        {
            $props[$this->database->escape_column_name($key)] = $value;
        }
        $props[$this->database->escape_column_name(SurveyContext :: PROPERTY_ID)] = $context->get_id();
        $props[$this->database->escape_column_name(SurveyContext :: PROPERTY_TYPE)] = $context->get_type();
        $props[$this->database->escape_column_name(SurveyContext :: PROPERTY_ID)] = $this->database->get_better_next_id('survey_context', 'id');
        $this->database->get_connection()->loadModule('Extended');
        $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('survey_context'), $props, MDB2_AUTOQUERY_INSERT);
        $context->set_id($this->database->get_connection()->extended->getAfterID($props[$this->database->escape_column_name(SurveyContext :: PROPERTY_ID)], 'survey_context'));
        if ($context->get_additional_properties())
        {
            $props = array();
            foreach ($context->get_additional_properties() as $key => $value)
            {
                $props[$this->database->escape_column_name($key)] = $value;
            }
            $props[$this->database->escape_column_name(SurveyContext :: PROPERTY_ID)] = $context->get_id();
            $this->database->get_connection()->extended->autoExecute($this->database->get_table_name($context->get_type()), $props, MDB2_AUTOQUERY_INSERT);
        }
        return true;
    }

    function count_survey_context($condition = null)
    {
        ;
    }

    function retrieve_additional_survey_context_properties($survey_context)
    {
        $type = $survey_context->get_type();
        
        //        if (! $this->is_extended_type($type))
        //        {
        //            return array();
        //        }
        $array = array_map(array($this, 'escape_column_name'), $survey_context->get_additional_property_names());
        
        if (count($array) == 0)
        {
            $array = array("*");
        }
        
        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $this->database->escape_table_name($type) . ' WHERE ' . $this->database->escape_column_name(SurveyContext :: PROPERTY_ID) . '=' . $survey_context->get_id();
        
        $this->database->set_limit(1);
        $res = $this->query($query);
        $return = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $res->free();
        
        return $return;
    }

    // Inherited.
    function determine_survey_context_type($id)
    {
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
        $record = $this->database->retrieve_record(SurveyContext :: get_table_name(), $condition);
        return $record[SurveyContext :: PROPERTY_TYPE];
    }

    function record_to_survey_context($record, $additional_properties_known = false)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $defaultProp = array();
        foreach (SurveyContext :: get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }
        
        $survey_context = SurveyContext :: factory($record[SurveyContext :: PROPERTY_TYPE], $defaultProp);
        
        if ($additional_properties_known)
        {
            $properties = $survey_context->get_additional_property_names();
            
            $additionalProp = array();
            if (count($properties) > 0)
            {
                foreach ($properties as $prop)
                {
                    $additionalProp[$prop] = $record[$prop];
                }
            }
        }
        else
        {
            $additionalProp = null;
        }
        
        foreach ($additionalProp as $name => $value)
        {
            $survey_context->set_additional_property($name, $value);
        
        }
        
        return $survey_context;
    }

}
?>
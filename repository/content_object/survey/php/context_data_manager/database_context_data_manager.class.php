<?php
namespace repository\content_object\survey;

use repository\DatabaseRepositoryDataManager;
use user\UserDataManager;
use user\User;
use group\GroupDataManager;
use group\Group;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use repository\content_object\survey_page\SurveyPage;
use common\libraries\Translation;

use Exception;

require_once (dirname(__FILE__) . '/../survey_context_template.class.php');
require_once (dirname(__FILE__) . '/../survey_template_user.class.php');
require_once (dirname(__FILE__) . '/../survey_context_registration.class.php');
require_once (dirname(__FILE__) . '/../survey_context_template_rel_page.class.php');
require_once (dirname(__FILE__) . '/../survey_context.class.php');
require_once (dirname(__FILE__) . '/../survey_context_rel_user.class.php');

require_once (dirname(__FILE__) . '/context_data_manager_interface.php');
require_once Path :: get_repository_path() . 'lib/data_manager/database_repository_data_manager.class.php';

class DatabaseSurveyContextDataManager extends DatabaseRepositoryDataManager implements SurveyContextDataManagerInterface
{

    function retrieve_survey_contexts($type, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        
        $type_table = $this->escape_table_name($type);
        
        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $this->get_alias(SurveyContext :: get_table_name());
        
        $query .= ' JOIN ' . $type_table . ' AS ' . $this->get_alias($type) . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $this->get_alias(SurveyContext :: get_table_name())) . '=' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $this->get_alias($type));
        
        return $this->retrieve_object_set($query, $type, $condition, $offset, $count, $order_property);
    }

    function count_survey_contexts($type, $condition = null)
    {
        
        $type_table = $this->escape_table_name($type);
        
        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $this->get_alias(SurveyContext :: get_table_name());
        
        $query .= ' JOIN ' . $type_table . ' AS ' . $this->get_alias($type) . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $this->get_alias(SurveyContext :: get_table_name())) . '=' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $this->get_alias($type));
        
        return $this->count_result_set($query, $type, $condition);
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
        //        dump($type);
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
        //context is always extended because SurveyContext is an abstact class        
        //        if ($this->is_extended_type($type))
        //        {
        $survey_context_alias = $this->get_alias(SurveyContext :: get_table_name());
        
        //dump($condition);
        

        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $survey_context_alias;
        $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $survey_context_alias) . '=' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, self :: ALIAS_TYPE_TABLE);
        
        //dump($query);
        

        $record = $this->retrieve_row($query, SurveyContext :: get_table_name(), $condition);
        //        }
        //        else
        //        {
        //            $record = $this->retrieve_record(SurveyContext :: get_table_name(), $condition);
        //        }
        

        return self :: record_to_survey_context($record, isset($type));
    }

    function delete_survey_context($context)
    {
        
        if ($context->get_additional_properties())
        {
            $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
            $this->delete_objects(Utilities :: get_classname_from_namespace(get_class($context), true), $condition);
        }
        
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
        $success = $this->delete_objects(SurveyContext :: get_table_name(), $condition);
        return $success;
    }

    function update_survey_context($context)
    {
        
        if ($context->get_additional_properties())
        {
            $properties = Array();
            $alias = $this->get_alias(Utilities :: get_classname_from_namespace(get_class($context), true));
            foreach ($context->get_additional_property_names() as $property_name)
            {
                $properties[$property_name] = $this->quote($context->get_additional_property($property_name));
            }
            
            $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
            $this->update_objects(Utilities :: get_classname_from_namespace(get_class($context), true), $properties, $condition);
        }
        
        $props = array();
        $props[SurveyContext :: PROPERTY_ACTIVE] = $this->quote($context->get_default_property(SurveyContext :: PROPERTY_ACTIVE));
        $props[SurveyContext :: PROPERTY_NAME] = $this->quote($context->get_default_property(SurveyContext :: PROPERTY_NAME));
        
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $context->get_id());
        $this->update_objects(Utilities :: get_classname_from_namespace(SurveyContext :: CLASS_NAME, true), $props, $condition);
        return true;
    }

    function create_survey_context_template($survey_context_template)
    {
        return $this->create($survey_context_template);
    }

    function create_survey_context($context)
    {
        $props = array();
        foreach ($context->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        
        //        $props[$this->escape_column_name(SurveyContext :: PROPERTY_ID)] = $context->get_id();
        //        $props[$this->escape_column_name(SurveyContext :: PROPERTY_TYPE)] = $context->get_type();
        //        $props[$this->escape_column_name(SurveyContext :: PROPERTY_ACTIVE)] = $context->get_active();
        //        $props[$this->escape_column_name(SurveyContext :: PROPERTY_CONTEXT_REGISTRATION_ID)] = $context->get_context_registration_id();
        $props[$this->escape_column_name(SurveyContext :: PROPERTY_ID)] = $this->get_better_next_id('survey_context', 'id');
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name('survey_context'), $props, MDB2_AUTOQUERY_INSERT);
        $context->set_id($this->get_connection()->extended->getAfterID($props[$this->escape_column_name(SurveyContext :: PROPERTY_ID)], 'survey_context'));
        if ($context->get_additional_properties())
        {
            $props = array();
            foreach ($context->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $props[$this->escape_column_name(SurveyContext :: PROPERTY_ID)] = $context->get_id();
            $this->get_connection()->extended->autoExecute($this->get_table_name($context->get_type()), $props, MDB2_AUTOQUERY_INSERT);
        }
        return true;
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
        
        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $this->escape_table_name($type) . ' WHERE ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID) . '=' . $survey_context->get_id();
        
        $this->set_limit(1);
        $res = $this->query($query);
        $return = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $res->free();
        
        return $return;
    }

    // Inherited.
    function determine_survey_context_type($id)
    {
        $condition = new EqualityCondition(SurveyContext :: PROPERTY_ID, $id);
        $record = $this->retrieve_record(SurveyContext :: get_table_name(), $condition);
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

    function retrieve_survey_context_templates($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(SurveyContextTemplate :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyContextTemplate :: CLASS_NAME);
    }

    function retrieve_survey_context_template($survey_context_template_id)
    {
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_ID, $survey_context_template_id);
        return $this->retrieve_object(SurveyContextTemplate :: get_table_name(), $condition, array(), SurveyContextTemplate :: CLASS_NAME);
    }

    function count_survey_context_templates($condition = null)
    {
        return $this->count_objects(SurveyContextTemplate :: get_table_name(), $condition);
    }

    function update_survey_context_template($survey_context_template)
    {
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_ID, $survey_context_template->get_id());
        return $this->update($survey_context_template, $condition);
    }

    function delete_survey_context_template($survey_context_template)
    {
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_ID, $survey_context_template->get_id());
        return $this->delete(SurveyContextTemplate :: get_table_name(), $condition);
    }

    function truncate_survey_context_template($survey_id, $template_id)
    {
        
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $template_id);
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id);
        $condition = new AndCondition($conditions);
        
        $template_rel_pages = $this->retrieve_template_rel_pages($condition);
        while ($template_rel_page = $template_rel_pages->next_result())
        {
            $this->delete_survey_context_template_rel_page($template_rel_page);
        }
        return true;
    }

    function create_survey_template($survey_template)
    {
        return $this->create($survey_template);
    }

    function retrieve_survey_templates($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(SurveyTemplate :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyTemplate :: CLASS_NAME);
    }

    function retrieve_survey_template($survey_template_id)
    {
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_ID, $survey_template_id);
        return $this->retrieve_object(SurveyTemplate :: get_table_name(), $condition, array(), SurveyTemplate :: CLASS_NAME);
    }

    function count_survey_templates($condition = null)
    {
        return $this->count_objects(SurveyTemplate :: get_table_name(), $condition);
    }

    function update_survey_template($survey_template)
    {
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_ID, $survey_template->get_id());
        return $this->update($survey_template, $condition);
    }

    function delete_survey_template($survey_template)
    {
        $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_ID, $survey_template->get_id());
        return $this->delete(SurveyTemplate :: get_table_name(), $condition);
    }

    function truncate_survey_template($template_id)
    {
        
        $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_TEMPLATE_ID, $template_id);
        
        $template_users = $this->retrieve_survey_template_users($condition);
        while ($template_users = $template_users->next_result())
        {
            $this->delete_survey_template_user($template_users);
        }
        return true;
    }

    function retrieve_template_rel_pages($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rel_alias = $this->get_alias(SurveyContextTemplateRelPage :: get_table_name());
        $template_alias = $this->get_alias(SurveyContextTemplate :: get_table_name());
        $page_alias = $this->get_alias(SurveyPage :: get_table_name());
        
        $query = 'SELECT ' . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextTemplateRelPage :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(SurveyPage :: get_table_name()) . ' AS ' . $page_alias . ' ON ' . $this->escape_column_name(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $rel_alias) . ' = ' . $this->escape_column_name(SurveyPage :: PROPERTY_ID, $page_alias);
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContextTemplate :: get_table_name()) . ' AS ' . $template_alias . ' ON ' . $this->escape_column_name(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $rel_alias) . ' = ' . $this->escape_column_name(SurveyContextTemplate :: PROPERTY_ID, $template_alias);
        return $this->retrieve_object_set($query, SurveyContextTemplateRelPage :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyContextTemplateRelPage :: CLASS_NAME);
    }

    function count_template_rel_pages($condition = null)
    {
        
        $rel_alias = $this->get_alias(SurveyContextTemplateRelPage :: get_table_name());
        
        $template_alias = $this->get_alias(SurveyContextTemplate :: get_table_name());
        $page_alias = $this->get_alias(SurveyPage :: get_table_name());
        
        $query = 'SELECT ' . ' COUNT(*) ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextTemplateRelPage :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(SurveyPage :: get_table_name()) . ' AS ' . $page_alias . ' ON ' . $this->escape_column_name(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $rel_alias) . ' = ' . $this->escape_column_name(SurveyPage :: PROPERTY_ID, $page_alias);
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContextTemplate :: get_table_name()) . ' AS ' . $template_alias . ' ON ' . $this->escape_column_name(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $rel_alias) . ' = ' . $this->escape_column_name(SurveyContextTemplate :: PROPERTY_ID, $template_alias);
        
        return $this->count_result_set($query, SurveyContextTemplateRelPage :: get_table_name(), $condition);
    
    }

    function create_survey_context_template_rel_page($survey_context_template_rel_page)
    {
        return $this->create($survey_context_template_rel_page, false);
    }

    function delete_survey_context_template_rel_page($survey_context_template_rel_page)
    {
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $survey_context_template_rel_page->get_page_id());
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_context_template_rel_page->get_survey_id());
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $survey_context_template_rel_page->get_template_id());
        $condition = new AndCondition($conditions);
        return $this->delete_objects(SurveyContextTemplateRelPage :: get_table_name(), $condition);
    }

    function retrieve_survey_context_registrations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(SurveyContextRegistration :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyContextRegistration :: CLASS_NAME);
    }

    function retrieve_survey_context_registration($survey_context_registration_id)
    {
        $condition = new EqualityCondition(SurveyContextRegistration :: PROPERTY_ID, $survey_context_registration_id);
        return $this->retrieve_object(SurveyContextRegistration :: get_table_name(), $condition, array(), SurveyContextRegistration :: CLASS_NAME);
    }

    function count_survey_context_registrations($condition = null)
    {
        return $this->count_objects(SurveyContextRegistration :: get_table_name(), $condition);
    }

    function create_survey_context_registration($survey_context_registration)
    {
        return $this->create($survey_context_registration);
    }

    function delete_survey_context_registration($survey_context_registration)
    {
        $condition = new EqualityCondition(SurveyContextRegistration :: PROPERTY_ID, $survey_context_registration->get_id());
        return $this->delete_objects(SurveyContextRegistration :: get_table_name(), $condition);
    }

    function update_survey_context_registration($survey_context_registration)
    {
        $condition = new EqualityCondition(SurveyContextRegistration :: PROPERTY_ID, $survey_context_registration->get_id());
        return $this->update($survey_context_registration, $condition);
    }

    function retrieve_survey_template_users($type, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        
        require_once dirname(__FILE__) . '/../template/' . $type . '/' . $type . '.class.php';
        
        $type_table = $this->escape_table_name($type);
        
        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyTemplateUser :: get_table_name()) . ' AS ' . $this->get_alias(SurveyTemplateUser :: get_table_name());
        
        $query .= ' JOIN ' . $type_table . ' AS ' . $this->get_alias($type) . ' ON ' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, $this->get_alias(SurveyTemplateUser :: get_table_name())) . '=' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, $this->get_alias($type));
        
        return $this->retrieve_object_set($query, $type, $condition, $offset, $count, $order_property);
    }

    function count_survey_template_users($type, $condition = null)
    {
        
        require_once dirname(__FILE__) . '/../template/' . $type . '/' . $type . '.class.php';
        
        $type_table = $this->escape_table_name($type);
        
        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(SurveyTemplateUser :: get_table_name()) . ' AS ' . $this->get_alias(SurveyTemplateUser :: get_table_name());
        
        $query .= ' JOIN ' . $type_table . ' AS ' . $this->get_alias($type) . ' ON ' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, $this->get_alias(SurveyTemplateUser :: get_table_name())) . '=' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, $this->get_alias($type));
        
        return $this->count_result_set($query, $type, $condition);
    }

    function retrieve_survey_template_user_by_id($id, $type)
    {
        if (! isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID || $id == 0)
        {
            return null;
        }
        
        if (is_null($type))
        {
            $type = $this->determine_survey_template_user_type($id);
        }
        
        $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_ID, $id);
        
        $survey_template_user_alias = $this->get_alias(SurveyTemplateUser :: get_table_name());
        
        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyTemplateUser :: get_table_name()) . ' AS ' . $survey_template_user_alias;
        $query .= ' JOIN ' . $this->escape_table_name($type) . ' AS ' . self :: ALIAS_TYPE_TABLE . ' ON ' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, $survey_template_user_alias) . '=' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID, self :: ALIAS_TYPE_TABLE);
        
        $record = $this->retrieve_row($query, SurveyTemplateUser :: get_table_name(), $condition);
        
        return self :: record_to_survey_template_user($record, isset($type));
    }

    function delete_survey_template_user($template)
    {
        
        if ($template->get_additional_properties())
        {
            $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_ID, $template->get_id());
            $succes = $this->delete_objects(Utilities :: get_classname_from_namespace(get_class($template), true), $condition);
        }
        
        $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_ID, $template->get_id());
        $succes = $this->delete_objects(SurveyTemplateUser :: get_table_name(), $condition);
        return $succes;
    }

    function update_survey_template_user($template_user)
    {
        
        $succes = false;
    	if ($template_user->get_additional_properties())
        {
            $properties = Array();
            $alias = $this->get_alias(Utilities :: get_classname_from_namespace(get_class($template_user), true));
            foreach ($template->get_additional_property_names() as $property_name)
            {
                $properties[$property_name] = $this->quote($template_user->get_additional_property($property_name));
            }
            $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_ID, $template_user->get_id());
            $succes = $this->update_objects(Utilities :: get_classname_from_namespace(get_class($template_user), true), $properties, $condition);
        }
        return $succes;
    }

    function create_survey_template_user($template_user)
    {
    	$props = array();
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyTemplateUser:: PROPERTY_USER_ID,  $template_user->get_user_id(), SurveyTemplateUser :: get_table_name());
        $conditions[] =new EqualityCondition(SurveyTemplateUser:: PROPERTY_TEMPLATE_ID, $template_user->get_template_id(),  SurveyTemplateUser :: get_table_name());
       	
    	foreach ($template_user->get_additional_properties() as $key => $value)
        {
           
            $conditions[] =new EqualityCondition($key, $value, $template_user->get_type());
            
        }
        
        $condition = new AndCondition($conditions);
        $existing_template_user = $this->retrieve_survey_template_users($template_user->get_type(), $condition)->next_result();
      
        if (isset($existing_template_user)){
        	//template users have be unique
        	return false;	
        }
        
        foreach ($template_user->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
     
        //        $props[$this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID)] = $template_user->get_id();
        $props[$this->escape_column_name(SurveyTemplateUser :: PROPERTY_TYPE)] = $template_user->get_type();
        $props[$this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID)] = $this->get_better_next_id('survey_template_user', 'id');
        $this->get_connection()->loadModule('Extended');
        $this->get_connection()->extended->autoExecute($this->get_table_name('survey_template_user'), $props, MDB2_AUTOQUERY_INSERT);
        $template_user->set_id($this->get_connection()->extended->getAfterID($props[$this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID)], 'survey_template_user'));
        
        if ($template_user->get_additional_properties())
        {
            $props = array();
            foreach ($template_user->get_additional_properties() as $key => $value)
            {
                $props[$this->escape_column_name($key)] = $value;
            }
            $props[$this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID)] = $template_user->get_id();
            $this->get_connection()->extended->autoExecute($this->get_table_name($template_user->get_type()), $props, MDB2_AUTOQUERY_INSERT);
        }
        return true;
    }

    function retrieve_additional_survey_template_user_properties($survey_template_user)
    {
        $type = $survey_template_user->get_type();
        
        $array = array_map(array($this, 'escape_column_name'), $survey_template_user->get_additional_property_names());
        
        if (count($array) == 0)
        {
            $array = array("*");
        }
        
        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $this->escape_table_name($type) . ' WHERE ' . $this->escape_column_name(SurveyTemplateUser :: PROPERTY_ID) . '=' . $survey_template_user->get_id();
        
        $this->set_limit(1);
        $res = $this->query($query);
        $return = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $res->free();
        
        return $return;
    }

    // Inherited.
    function determine_survey_template_user_type($id)
    {
        $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_ID, $id);
        $record = $this->retrieve_record(SurveyTemplateUser :: get_table_name(), $condition);
        return $record[SurveyTemplateUser :: PROPERTY_TYPE];
    }

    function record_to_survey_template_user($record, $additional_properties_known = false)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $defaultProp = array();
        foreach (SurveyTemplateUser :: get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }
        
        $survey_template_user = SurveyTemplateUser :: factory($record[SurveyTemplateUser :: PROPERTY_TYPE], $defaultProp);
        
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
            $survey_template_user->set_additional_property($name, $value);
        
        }
        
        return $survey_template_user;
    }

    function delete_survey_context_rel_user($context_rel_user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $context_rel_user->get_user_id());
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_rel_user->get_context_id());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($context_rel_user->get_table_name(), $condition);
        return $bool;
    }

    function create_survey_context_rel_user($context_rel_user)
    {
        return $this->create($context_rel_user);
    }

    function count_survey_context_rel_users($condition = null)
    {
        $context_alias = $this->get_alias(SurveyContext :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $context_rel_user_alias = $this->get_alias(SurveyContextRelUser :: get_table_name());
        
        $query = 'SELECT COUNT(* ) ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextRelUser :: get_table_name()) . ' AS ' . $context_rel_user_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $context_alias . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $context_alias) . ' = ' . $this->escape_column_name(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_rel_user_alias);
        
        $query .= ' JOIN ' . UserDataManager :: get_instance()->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $this->escape_column_name(SurveyContextRelUser :: PROPERTY_USER_ID, $context_rel_user_alias) . ' = ' . $this->escape_column_name(User :: PROPERTY_ID, $user_alias);
        
        return $this->count_result_set($query, SurveyContextRelUser :: get_table_name(), $condition);
    
    }

    function retrieve_survey_context_rel_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $context_alias = $this->get_alias(SurveyContext :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $context_rel_user_alias = $this->get_alias(SurveyContextRelUser :: get_table_name());
        
        $query = 'SELECT ' . $context_rel_user_alias . '.*  ,' . $user_alias . '.* ,' . $context_alias . '.* ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextRelUser :: get_table_name()) . ' AS ' . $context_rel_user_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $context_alias . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $context_alias) . ' = ' . $this->escape_column_name(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_rel_user_alias);
        
        $query .= ' JOIN ' . UserDataManager :: get_instance()->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $this->escape_column_name(SurveyContextRelUser :: PROPERTY_USER_ID, $context_rel_user_alias) . ' = ' . $this->escape_column_name(User :: PROPERTY_ID, $user_alias);
        
        return $this->retrieve_object_set($query, SurveyContextRelUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyContextRelUser :: CLASS_NAME);
    }

    function retrieve_survey_context_rel_user($context_id, $user_id)
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_CONTEXT_ID, $context_id);
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(SurveyContextRelUser :: get_table_name(), $condition, array(), SurveyContextRelUser :: CLASS_NAME);
    }

    //surveycontextreluser
    

    function delete_survey_context_rel_group($context_rel_group)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_GROUP_ID, $context_rel_group->get_group_id());
        $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $context_rel_group->get_context_id());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($context_rel_group->get_table_name(), $condition);
        if ($bool)
        {
            $group = GroupDataManager :: get_instance()->retrieve_group($context_rel_group->get_group_id());
            $user_ids = $group->get_users(true, true);
            $condition = new InCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_ids);
            $context_rel_users = $this->delete(SurveyContextRelUser :: get_table_name(), $condition);
        }
        
        return $bool;
    }

    function create_survey_context_rel_group($context_rel_group)
    {
        return $this->create($context_rel_group);
    }

    function count_survey_context_rel_groups($condition = null)
    {
        $context_alias = $this->get_alias(SurveyContext :: get_table_name());
        $group_alias = groupDataManager :: get_instance()->get_alias(group :: get_table_name());
        $context_rel_group_alias = $this->get_alias(SurveyContextRelGroup :: get_table_name());
        
        $query = 'SELECT COUNT(* ) ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextRelGroup :: get_table_name()) . ' AS ' . $context_rel_group_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $context_alias . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $context_alias) . ' = ' . $this->escape_column_name(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $context_rel_group_alias);
        
        $query .= ' JOIN ' . groupDataManager :: get_instance()->escape_table_name(group :: get_table_name()) . ' AS ' . $group_alias . ' ON ' . $this->escape_column_name(SurveyContextRelGroup :: PROPERTY_GROUP_ID, $context_rel_group_alias) . ' = ' . $this->escape_column_name(group :: PROPERTY_ID, $group_alias);
        
        return $this->count_result_set($query, SurveyContextRelGroup :: get_table_name(), $condition);
    
    }

    function retrieve_survey_context_rel_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        
        $context_alias = $this->get_alias(SurveyContext :: get_table_name());
        $group_alias = groupDataManager :: get_instance()->get_alias(group :: get_table_name());
        $context_rel_group_alias = $this->get_alias(SurveyContextRelGroup :: get_table_name());
        
        $query = 'SELECT ' . $context_rel_group_alias . '.*  ,' . $group_alias . '.* ';
        $query .= ' FROM ' . $this->escape_table_name(SurveyContextRelGroup :: get_table_name()) . ' AS ' . $context_rel_group_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(SurveyContext :: get_table_name()) . ' AS ' . $context_alias . ' ON ' . $this->escape_column_name(SurveyContext :: PROPERTY_ID, $context_alias) . ' = ' . $this->escape_column_name(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $context_rel_group_alias);
        
        $query .= ' JOIN ' . groupDataManager :: get_instance()->escape_table_name(group :: get_table_name()) . ' AS ' . $group_alias . ' ON ' . $this->escape_column_name(SurveyContextRelGroup :: PROPERTY_GROUP_ID, $context_rel_group_alias) . ' = ' . $this->escape_column_name(group :: PROPERTY_ID, $group_alias);
        
        return $this->retrieve_object_set($query, SurveyContextRelGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyContextRelGroup :: CLASS_NAME);
    
    }

    function retrieve_survey_context_rel_group($context_id, $group_id)
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_CONTEXT_ID, $context_id);
        $conditions[] = new EqualityCondition(SurveyContextRelGroup :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(SurveyContextRelGroup :: get_table_name(), $condition, array(), SurveyContextRelGroup :: CLASS_NAME);
    }

}
?>
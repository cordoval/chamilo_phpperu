<?php
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../reporting_data_manager_interface.class.php';
/**
 * $Id: database_reporting_data_manager.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package reporting.lib.data_manager
 * @author Michael Kyndt
 */

class DatabaseReportingDataManager extends Database implements ReportingDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('reporting_');
    }

    /**
     * Creates a reporting block in the database
     * @param ReportingBlock $reporting_block
     */
    function create_reporting_block($reporting_block)
    {
        return $this->create($reporting_block);
    }

    /**
     * Updates an reporting block (needed for change of activity)
     * @param ReportingBlock $reporting_block
     */
    function update_reporting_block($reporting_block)
    {
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_ID, $reporting_block->get_id());
        return $this->update($reporting_block, $condition);
    }

    /**
     * Retrieves the reporting block with the given name
     * @param String $name
     * @return ReportingBlock $reporting_block
     */
    function retrieve_reporting_block_by_name($blockname)
    {
        $condition = new EqualityCondition(ReportingBlockRegistration :: PROPERTY_BLOCK, $blockname);
        return $this->retrieve_object(ReportingBlockRegistration :: get_table_name(), $condition);
    }

    /**
     * Retrieves all reporting blocks
     * @return array of reporting blocks
     */
    function retrieve_reporting_blocks($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ReportingBlock :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    /**
     * Count reporting blocks for a given condition
     * @param Condition $condition
     * @return Int reporting block count
     */
    function count_reporting_blocks($condition = null)
    {
        return $this->count_objects(ReportingBlock :: get_table_name(), $condition);
    }

    /**
     * Retrieves a reporting block by given id
     * @param int $reporting_block_id
     * @return ReportingBlock $reporting_block
     */
    function retrieve_reporting_block($reporting_block_id)
    {
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_ID, $reporting_block_id);
        return $this->retrieve_object(ReportingBlock :: get_table_name(), $condition);
    }

    function create_reporting_template_registration($reporting_template_registration)
    {
        return $this->create($reporting_template_registration);
    } //create_reporting_template_registration

    function create_reporting_block_registration($reporting_block_registration)
    {
        return $this->create($reporting_block_registration);
    } //create_reporting_template_registration


    function update_reporting_template_registration($reporting_template_registration)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_template_registration->get_id());
        return $this->update($reporting_template_registration, $condition);
    } //update_reporting_template_registration


    //function retrieve_reporting_template_registration_by_name($reporting_template_registration_name)
    //{
    //$condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_NAME, $reporting_template_registration_name);
    //return $this->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    //}//retrieve_reporting_template_registration_by_name


    function retrieve_reporting_template_registrations($condition = null, $offset = null, $max_objects = null, $order_property = null)
    {
        return $this->retrieve_objects(ReportingTemplateRegistration :: get_table_name(), $condition, $offset, $max_objects, $order_property);
    } //retrieve_reporting_template_registrations

    function retrieve_reporting_block_registrations($condition = null, $offset = null, $max_objects = null, $order_property = null)
    {
    	return $this->retrieve_objects(ReportingBlockRegistration :: get_table_name(), $condition, $offset, $max_objects, $order_property);
    }

    function count_reporting_template_registrations($condition = null)
    {
        return $this->count_objects(ReportingTemplateRegistration :: get_table_name(), $condition);
    } //count_reporting_template_registrations


    function retrieve_reporting_template_registration($reporting_template_registration_id)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_template_registration_id);
        return $this->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    } //retrieve_reporting_template_registration

    function retrieve_reporting_template_registration_by_condition($condition)
    {
        return $this->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    } 
    
    function retrieve_reporting_block_registration($reporting_block_registration_id)
    {
    	$condition = new EqualityCondition(ReportingBlockRegistration:: PROPERTY_ID, $reporting_block_registration_id);
    	return $this->retrieve_object(ReportingBlockRegistration::get_table_name(), $condition);
    }

	function delete_reporting_block_registrations($condition = null)
    {
        return $this->delete_objects(ReportingBlockRegistration :: get_table_name(), $condition);
    }
    
    function delete_reporting_template_registrations($condition = null)
    {
        return $this->delete_objects(ReportingTemplateRegistration :: get_table_name(), $condition);
    }

    function delete_reporting_blocks($condition = null)
    {
        return $this->delete_objects(ReportingBlock :: get_table_name(), $condition);
    }

    function delete_orphaned_block_template_relations()
    {
        $query = 'DELETE FROM ' . $this->escape_table_name('reporting_template_registration_rel_reporting_block') . ' WHERE ';
        $query .= $this->escape_column_name('reporting_template_registration_id') . ' NOT IN (SELECT ' . $this->escape_column_name(ReportingTemplateRegistration :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name(ReportingTemplateRegistration :: get_table_name()) . ') OR ';
        $query .= $this->escape_column_name('reporting_block_id') . ' NOT IN (SELECT ' . $this->escape_column_name(ReportingBlock :: PROPERTY_ID) . ' FROM ' . $this->escape_table_name(ReportingBlock :: get_table_name()) . ')';
        $res = $this->query($query);
        $res->free();
        return $res;
    }

    function retrieve_reporting_template_object($classname)
    {
    	$condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_TEMPLATE, $classname);

    	return $this->retrieve_object(ReportingTemplateRegistration :: get_table_name(), $condition);
    }
}
?>
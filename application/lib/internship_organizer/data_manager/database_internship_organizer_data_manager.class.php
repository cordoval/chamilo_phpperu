<?php
/**
 * @package internship_organizer.datamanager
 */

require_once dirname(__FILE__) . '/../internship_organizer_data_manager.interface.php';

require_once dirname(__FILE__) . '/../category.class.php';
require_once dirname(__FILE__) . '/../location.class.php';
require_once dirname(__FILE__) . '/../category_rel_location.class.php';
require_once dirname(__FILE__) . '/../category_rel_period.class.php';
require_once dirname(__FILE__) . '/../organisation.class.php';
require_once dirname(__FILE__) . '/../organisation_rel_user.class.php';
require_once dirname(__FILE__) . '/../agreement.class.php';
require_once dirname(__FILE__) . '/../agreement_rel_location.class.php';
require_once dirname(__FILE__) . '/../agreement_rel_user.class.php';
require_once dirname(__FILE__) . '/../moment.class.php';
require_once dirname(__FILE__) . '/../region.class.php';
require_once dirname(__FILE__) . '/../period.class.php';
require_once dirname(__FILE__) . '/../period_rel_user.class.php';
require_once dirname(__FILE__) . '/../period_rel_group.class.php';
require_once dirname(__FILE__) . '/../user_type.class.php';

require_once 'MDB2.php';

class DatabaseInternshipOrganizerDataManager extends Database implements InternshipOrganizerDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('internship_organizer_');
    
    }

    //	function create_storage_unit($name, $properties, $indexes) {
    //		return $this->create_storage_unit ( $name, $properties, $indexes );
    //	}
    

    //internship planner locations
    

    function create_internship_organizer_location($location)
    {
        return $this->create($location);
    }

    function update_internship_organizer_location($location)
    {
        $condition = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $location->get_id());
        return $this->update($location, $condition);
    }

    function delete_internship_organizer_location($location)
    {
        $condition = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $location->get_id());
        return $this->delete($location->get_table_name(), $condition);
    }

    function count_locations($condition = null)
    {
        return $this->count_objects(InternshipOrganizerLocation :: get_table_name(), $condition);
    }

    function retrieve_location($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerLocation :: get_table_name(), $condition, array(), InternshipOrganizerLocation :: CLASS_NAME);
    }

    function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerLocation :: CLASS_NAME);
    }

    //internship planner organisations
    

    function create_internship_organizer_organisation($organisation)
    {
        return $this->create($organisation);
    }

    function update_internship_organizer_organisation($organisation)
    {
        $condition = new EqualityCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation->get_id());
        return $this->update($organisation, $condition);
    }

    function delete_internship_organizer_organisation($organisation)
    {
        $condition = new EqualityCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation->get_id());
        return $this->delete($organisation->get_table_name(), $condition);
    }

    function count_organisations($condition = null)
    {
        return $this->count_objects(InternshipOrganizerOrganisation :: get_table_name(), $condition);
    }

    function retrieve_organisation($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerOrganisation :: get_table_name(), $condition, array(), InternshipOrganizerOrganisation :: CLASS_NAME);
    }

    function retrieve_organisations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerOrganisation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerOrganisation :: CLASS_NAME);
    }

    function delete_internship_organizer_organisation_rel_user($organisation_rel_user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_USER_ID, $organisation_rel_user->get_user_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $organisation_rel_user->get_organisation_id());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($organisation_rel_user->get_table_name(), $condition);
        return $bool;
    }

    function create_internship_organizer_organisation_rel_user($organisation_rel_user)
    {
        return $this->create($organisation_rel_user);
    }

    function count_organisation_rel_users($condition = null)
    {
        return $this->count_objects(InternshipOrganizerOrganisationRelUser :: get_table_name(), $condition);
    }

    function retrieve_organisation_rel_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerOrganisationRelUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerOrganisationRelUser :: CLASS_NAME);
    }

    //internship planner categories
    

    function update_internship_organizer_category($category)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, $category->get_id());
        return $this->update($category, $condition);
    }

    function delete_internship_organizer_category($category)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, $category->get_id());
        $bool = $this->delete($category->get_table_name(), $condition);
        
        $condition_subcategories = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, $category->get_id());
        $categories = $this->retrieve_categories($condition_subcategories);
        while ($gr = $categories->next_result())
        {
            $bool = $bool & $this->delete_internship_organizer_category($gr);
        }
        
        $this->truncate_category($category);
        
        return $bool;
    
    }

    function truncate_category($category)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category->get_id());
        return $this->delete(InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition);
    }

    function delete_category_rel_location($categoryrellocation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $categoryrellocation->get_category_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $categoryrellocation->get_location_id());
        $condition = new AndCondition($conditions);
        
        return $this->delete($categoryrellocation->get_table_name(), $condition);
    }

    function create_internship_organizer_category($category)
    {
        return $this->create($category);
    }

    function create_internship_organizer_category_rel_location($categoryrellocation)
    {
        return $this->create($categoryrellocation);
    }

    function count_categories($condition = null)
    {
        return $this->count_objects(InternshipOrganizerCategory :: get_table_name(), $condition);
    }

    function retrieve_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategory :: CLASS_NAME);
    }

    function retrieve_internship_organizer_category($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerCategory :: get_table_name(), $condition, array(), InternshipOrganizerCategory :: CLASS_NAME);
    }

    function retrieve_full_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        
        $rel_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
        
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());
        
        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $rel_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);
        
        return $this->retrieve_object_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
    }

    function count_full_category_rel_locations($condition = null)
    {
        $rel_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
        
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());
        
        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $rel_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);
        
        return $this->count_result_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition);
    }

    function retrieve_period_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        
        $category_rel__location_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
        $category_rel_period_alias = $this->get_alias(InternshipOrganizerCategoryRelPeriod :: get_table_name());
        
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());
        
        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $category_rel__location_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerCategoryRelPeriod :: get_table_name()) . ' AS ' . $category_rel_period_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelPeriod :: PROPERTY_CATEGORY_ID, $category_rel__location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category_rel__location_alias);
        
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $category_rel__location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);
        
        return $this->retrieve_object_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
    }

    function count_period_rel_locations($condition = null)
    {
        
        $category_rel__location_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
        $category_rel_period_alias = $this->get_alias(InternshipOrganizerCategoryRelPeriod :: get_table_name());
        
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());
        
        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $category_rel__location_alias;
        
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerCategoryRelPeriod :: get_table_name()) . ' AS ' . $category_rel_period_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelPeriod :: PROPERTY_CATEGORY_ID, $category_rel__location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category_rel__location_alias);
        
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $category_rel__location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);
        
        return $this->count_result_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition);
    }

    function count_category_rel_locations($condition = null)
    {
        return $this->count_objects(InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition);
    }

    function retrieve_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
    }

    function retrieve_category_rel_location($location_id, $category_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $location_id);
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $category_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition, array(), InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
    }

    function retrieve_organisation_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerLocation :: CLASS_NAME);
    }

    function retrieve_category_by_name($name)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_NAME, $name);
        return $this->retrieve_object(InternshipOrganizerCategory :: get_table_name(), $condition);
    }

    function is_categoryname_available($categoryname, $category_id = null)
    {
        $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_NAME, $categoryname);
        
        if ($category_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_NAME, $categoryname);
            $conditions = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_ID, $category_id);
            $condition = new AndCondition($conditions);
        }
        
        return ! ($this->count_objects(InternshipOrganizerCategory :: get_table_name(), $condition) == 1);
    }

    function retrieve_root_category()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, 0);
        $condition = new AndCondition($conditions);
        $root_category = $this->retrieve_categories($condition)->next_result();
        if (! isset($root_category))
        {
            $root_category = new InternshipOrganizerCategory();
            $root_category->set_name('ROOT');
            $root_category->set_parent_id(0);
            $root_category->create();
        }
        return $root_category;
    }

    function delete_internship_organizer_category_rel_period($category_rel_period)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_CATEGORY_ID, $category_rel_period->get_category_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_PERIOD_ID, $category_rel_period->get_period_id());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($category_rel_period->get_table_name(), $condition);
        return $bool;
    }

    function create_internship_organizer_category_rel_period($category_rel_period)
    {
        return $this->create($category_rel_period);
    }

    function count_category_rel_periods($condition = null)
    {
        return $this->count_objects(InternshipOrganizerCategoryRelPeriod :: get_table_name(), $condition);
    }

    function retrieve_category_rel_periods($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerCategoryRelPeriod :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategoryRelPeriod :: CLASS_NAME);
    }

    //internship planner moments
    

    function create_internship_organizer_moment($moment)
    {
        return $this->create($moment);
    }

    function update_internship_organizer_moment($moment)
    {
        $condition = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_ID, $moment->get_id());
        return $this->update($moment, $condition);
    }

    function delete_internship_organizer_moment($moment)
    {
        $condition = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_ID, $moment->get_id());
        return $this->delete($moment->get_table_name(), $condition);
    }

    function count_moments($condition = null)
    {
        return $this->count_objects(InternshipOrganizerMoment :: get_table_name(), $condition);
    }

    function retrieve_moment($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerMoment :: get_table_name(), $condition, array(), InternshipOrganizerMoment :: CLASS_NAME);
    }

    function retrieve_moments($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerMoment :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerMoment :: CLASS_NAME);
    }

    //internship planner agreements
    

    function create_internship_organizer_agreement($agreement)
    {
        return $this->create($agreement);
    }

    function update_internship_organizer_agreement($agreement)
    {
        $condition = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $agreement->get_id());
        return $this->update($agreement, $condition);
    }

    function delete_internship_organizer_agreement($agreement)
    {
        $condition = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $agreement->get_id());
        return $this->delete($agreement->get_table_name(), $condition);
    }

    function count_agreements($condition = null)
    {
        return $this->count_objects(InternshipOrganizerAgreement :: get_table_name(), $condition);
    }

    function retrieve_agreement($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerAgreement :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerAgreement :: get_table_name(), $condition, array(), InternshipOrganizerAgreement :: CLASS_NAME);
    }

    function retrieve_agreements($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerAgreement :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerAgreement :: CLASS_NAME);
    }

    function delete_internship_organizer_agreement_rel_user($agreement_rel_user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $agreement_rel_user->get_user_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_PERIOD_ID, $agreement_rel_user->get_period_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $agreement_rel_user->get_user_type());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($agreement_rel_user->get_table_name(), $condition);
        return $bool;
    }

    function create_internship_organizer_agreement_rel_user($agreement_rel_user)
    {
        return $this->create($agreement_rel_user);
    }

    function count_agreement_rel_users($condition = null)
    {
        return $this->count_objects(InternshipOrganizerAgreementRelUser :: get_table_name(), $condition);
    }

    function retrieve_agreement_rel_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerAgreementRelUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerAgreementRelUser :: CLASS_NAME);
    }

    function delete_internship_organizer_agreement_rel_location($agreement_rel_location)
    {
        
        $agreement_id = $agreement_rel_location->get_agreement_id();
        $location_id = $agreement_rel_location->get_location_id();
        
        $query = 'UPDATE ' . $this->escape_table_name('agreement_rel_location') . ' SET ' . $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER) . '=' . $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER) . '-1 WHERE ' . $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER) . '>' . $this->quote($agreement_rel_location->get_preference_order());
        $res = $this->query($query);
        $res->free();
        
        $query = 'DELETE FROM ' . $this->escape_table_name('agreement_rel_location') . ' WHERE ' . $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID) . '=' . $this->quote($agreement_id) . 'AND' . $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID) . '=' . $this->quote($location_id);
        $this->get_connection()->setLimit(0, 1);
        $res = $this->query($query);
        $res->free();
        
        return true;
        
    //    	$conditions = array();
    //        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_rel_location->get_agreement_id());
    //        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID, $agreement_rel_location->get_location_id());
    //        $condition = new AndCondition($conditions);
    //        $bool = $this->delete($agreement_rel_location->get_table_name(), $condition);
    //        return $bool;
    }

    function create_internship_organizer_agreement_rel_location($agreement_rel_location)
    {
        $preference_order = $this->get_next_agreement_rel_location_preference_order($agreement_rel_location->get_agreement_id());
        $agreement_rel_location->set_preference_order($preference_order);
        $succes = $this->create($agreement_rel_location);
        $agreement_rel_location->set_location_type(InternshipOrganizerAgreementRelLocation :: TO_APPROVE);
        return $succes;
    }

    function count_agreement_rel_locations($condition = null)
    {
        return $this->count_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $condition);
    }

    function retrieve_agreement_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerAgreementRelLocation :: CLASS_NAME);
    }

    function retrieve_agreement_rel_location($location_id, $agreement_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID, $location_id);
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(InternshipOrganizerAgreementRelLocation :: get_table_name(), $condition, array(), InternshipOrganizerAgreementRelLocation :: CLASS_NAME);
    }

    function get_next_agreement_rel_location_preference_order($agreement_id)
    {
        
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_id);
        return $this->retrieve_next_sort_value(InternshipOrganizerAgreementRelLocation :: get_table_name(), InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, $condition);
    }

    function move_agreement_rel_location($agreement_rel_location, $move_direction)
    {
        
        if ($move_direction < 0)
        {
            return $this->move_agreement_rel_location_up($agreement_rel_location, - $move_direction);
        }
        else
        {
            return $this->move_agreement_rel_location_down($agreement_rel_location, $move_direction);
        }
    }

    private function move_agreement_rel_location_up($agreement_rel_location, $move_direction)
    {
        $oldIndex = $agreement_rel_location->get_preference_order();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_rel_location->get_agreement_id());
        $conditions[] = new InequalityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, InequalityCondition :: LESS_THAN, $oldIndex);
        $condition = new AndCondition($conditions);
        
        $properties[InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER] = $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER) . '+1';
        
        if (! $this->update_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $properties, $condition, null, $move_direction, new ObjectTableOrder(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, SORT_DESC)))
        {
            return false;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_rel_location->get_agreement_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID, $agreement_rel_location->get_location_id());
        $condition = new AndCondition($conditions);
        
        $properties[InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER] = $oldIndex - $move_direction;
        
        return $this->update_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $properties, $condition, null, 1);
    }

    private function move_agreement_rel_location_down($agreement_rel_location, $move_direction)
    {
        $oldIndex = $agreement_rel_location->get_preference_order();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_rel_location->get_agreement_id());
        $conditions[] = new InequalityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, InequalityCondition :: GREATER_THAN, $oldIndex);
        $condition = new AndCondition($conditions);
        
        $properties[InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER] = $this->escape_column_name(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER) . '-1';
        
        if (! $this->update_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $properties, $condition, null, $move_direction, new ObjectTableOrder(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, SORT_ASC)))
        {
            return false;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_rel_location->get_agreement_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID, $agreement_rel_location->get_location_id());
        $condition = new AndCondition($conditions);
        
        $properties[InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER] = $oldIndex + $move_direction;
        
        return $this->update_objects(InternshipOrganizerAgreementRelLocation :: get_table_name(), $properties, $condition, null, 1);
    }

    //internship planner regions##
    

    function update_internship_organizer_region($region)
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, $region->get_id());
        return $this->update($region, $condition);
    }

    function delete_internship_organizer_region($region)
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, $region->get_id());
        $bool = $this->delete($region->get_table_name(), $condition);
        
        $condition_subregions = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, $region->get_id());
        $regions = $this->retrieve_regions($condition_subregions);
        while ($gr = $regions->next_result())
        {
            $bool = $bool & $this->delete_internship_organizer_region($gr);
            //mag dit? (i.e. recursieve oproep)
        }
        
        return $bool;
    
    }

    //
    //	function truncate_region($region) {
    //		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $region->get_id () );
    //		return $this->delete ( InternshipOrganizerRegion::get_table_name (), $condition );
    //	}
    //
    function create_internship_organizer_region($region)
    {
        return $this->create($region);
    }

    function count_regions($condition = null)
    {
        return $this->count_objects(InternshipOrganizerRegion :: get_table_name(), $condition);
    }

    function retrieve_regions($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerRegion :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerRegion :: CLASS_NAME);
    }

    function retrieve_internship_organizer_region($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerRegion :: get_table_name(), $condition, array(), InternshipOrganizerRegion :: CLASS_NAME);
    }

    function retrieve_region_by_name($name)
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_NAME, $name);
        return $this->retrieve_object(InternshipOrganizerRegion :: get_table_name(), $condition);
    }

    function is_regionname_available($regionname, $region_id = null)
    {
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_NAME, $regionname);
        
        if ($region_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_NAME, $regionname);
            $conditions = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ID, $region_id);
            $condition = new AndCondition($conditions);
        }
        
        return ! ($this->count_objects(InternshipOrganizerRegion :: get_table_name(), $condition) == 1);
    }

    function retrieve_root_region()
    {
        // 		$conditions = array();
        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0);
        // 		$condition = new AndCondition($conditions);
        $root_region = $this->retrieve_regions($condition)->next_result();
        if (! isset($root_region))
        {
            $root_region = new InternshipOrganizerRegion();
            $root_region->set_name(Translation :: get('World'));
            $root_region->set_parent_id(0);
            $root_region->create();
        
        }
        return $root_region;
    }

    //internship planner mentors
    

    function create_internship_organizer_mentor($mentor)
    {
        return $this->create($mentor);
    }

    function update_internship_organizer_mentor($mentor)
    {
        $condition = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $mentor->get_id());
        return $this->update($mentor, $condition);
    }

    function delete_internship_organizer_mentor($mentor)
    {
        $condition = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $mentor->get_id());
        return $this->delete($mentor->get_table_name(), $condition);
    }

    function count_mentors($condition = null)
    {
        return $this->count_objects(InternshipOrganizerMentor :: get_table_name(), $condition);
    }

    function retrieve_mentor($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerMentor :: get_table_name(), $condition, array(), InternshipOrganizerMentor :: CLASS_NAME);
    }

    function retrieve_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerMentor :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerMentor :: CLASS_NAME);
    }

    //internship planner periods##
    

    function update_internship_organizer_period($period)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $period->get_id());
        return $this->update($period, $condition);
    }

    function delete_internship_organizer_period($period)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $period->get_id());
        $bool = $this->delete($period->get_table_name(), $condition);
        
        $condition_subperiods = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, $period->get_id());
        $periods = $this->retrieve_periods($condition_subperiods);
        while ($gr = $periods->next_result())
        {
            $bool = $bool & $this->delete_internship_organizer_period($gr);
            //mag dit? (i.e. recursieve oproep)
        }
        
        return $bool;
    
    }

    function create_internship_organizer_period($period)
    {
        return $this->create($period);
    }

    function count_periods($condition = null)
    {
        return $this->count_objects(InternshipOrganizerPeriod :: get_table_name(), $condition);
    }

    function retrieve_periods($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerPeriod :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerPeriod :: CLASS_NAME);
    }

    function retrieve_period($period_id)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $period_id);
        return $this->retrieve_object(InternshipOrganizerPeriod :: get_table_name(), $condition, array(), InternshipOrganizerPeriod :: CLASS_NAME);
    }

    function retrieve_internship_organizer_period($id)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternshipOrganizerPeriod :: get_table_name(), $condition, array(), InternshipOrganizerPeriod :: CLASS_NAME);
    }

    function retrieve_period_by_name($name)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, $name);
        return $this->retrieve_object(InternshipOrganizerPeriod :: get_table_name(), $condition);
    }

    function is_periodname_available($periodname, $period_id = null)
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, $periodname);
        
        if ($period_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_NAME, $periodname);
            $conditions = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_ID, $period_id);
            $condition = new AndCondition($conditions);
        }
        
        return ! ($this->count_objects(InternshipOrganizerPeriod :: get_table_name(), $condition) == 1);
    }

    function retrieve_root_period()
    {
        $condition = new EqualityCondition(InternshipOrganizerPeriod :: PROPERTY_PARENT_ID, 0);
        $root_period = $this->retrieve_periods($condition)->next_result();
        if (! isset($root_period))
        {
            $root_period = new InternshipOrganizerPeriod();
            $root_period->set_name(Translation :: get('EhB'));
            $root_period->set_parent_id(0);
            $root_period->create();
        }
        return $root_period;
    }

    function delete_internship_organizer_period_rel_user($period_rel_user)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, $period_rel_user->get_user_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $period_rel_user->get_period_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $period_rel_user->get_user_type());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($period_rel_user->get_table_name(), $condition);
        return $bool;
    }

    function create_internship_organizer_period_rel_user($period_rel_user)
    {
        return $this->create($period_rel_user);
    }

    function count_period_rel_users($condition = null)
    {
        return $this->count_objects(InternshipOrganizerPeriodRelUser :: get_table_name(), $condition);
    }

    function retrieve_period_rel_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerPeriodRelUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerPeriodRelUser :: CLASS_NAME);
    }

    function delete_internship_organizer_period_rel_group($period_rel_group)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_GROUP_ID, $period_rel_group->get_group_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_PERIOD_ID, $period_rel_group->get_period_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelGroup :: PROPERTY_USER_TYPE, $period_rel_group->get_user_type());
        $condition = new AndCondition($conditions);
        $bool = $this->delete($period_rel_group->get_table_name(), $condition);
        return $bool;
    }

    function create_internship_organizer_period_rel_group($period_rel_group)
    {
        return $this->create($period_rel_group);
    }

    function count_period_rel_groups($condition = null)
    {
        return $this->count_objects(InternshipOrganizerPeriodRelGroup :: get_table_name(), $condition);
    }

    function retrieve_period_rel_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(InternshipOrganizerPeriodRelGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerPeriodRelGroup :: CLASS_NAME);
    }

    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        //        $condition = new InCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
    //        return $this->count_objects(SurveyPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(SurveyPublication :: get_table_name());
                
                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);
                
                $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
                $translator = new ConditionTranslator($this);
                $query .= $translator->render_query($condition);
                
                $order = array();
                foreach ($order_properties as $order_property)
                {
                    if ($order_property->get_property() == 'application')
                    {
                    
                    }
                    elseif ($order_property->get_property() == 'location')
                    {
                    
                    }
                    elseif ($order_property->get_property() == 'title')
                    {
                        $order[] = $this->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }
                
                if (count($order) > 0)
                    $query .= ' ORDER BY ' . implode(', ', $order);
            
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name());
            $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        
        }
        
        $this->set_limit($offset, $count);
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[SurveyPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
            $info->set_application(SurveyManager :: APPLICATION_NAME);
            //TODO: i8n location string
            $info->set_location(Translation :: get('Survey'));
            $info->set_url('run.php?application=survey&go=browse_surveys');
            $info->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
            
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' WHERE ' . $this->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->set_limit(0, 1);
        $res = $this->query($query);
        
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[SurveyPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application(SurveyManager :: APPLICATION_NAME);
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Survey'));
        $publication_attr->set_url('run.php?application=survey&go=browse_surveys');
        $publication_attr->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
        
        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->count_objects(SurveyPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_survey_publications($condition);
        
        $succes = true;
        
        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }
        
        return $succes;
    }

    function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $publication_id);
        return $this->delete(SurveyPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(SurveyPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
?>
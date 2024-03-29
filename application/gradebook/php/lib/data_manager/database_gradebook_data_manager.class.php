<?php
namespace application\gradebook;

use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Database;
use common\libraries\Request;

use user\UserDataManager;
use user\User;

use repository\RepositoryDataManager;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'internal_item_instance.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'external_item_instance.class.php';

class DatabaseGradebookDataManager extends Database implements GradebookDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('gradebook_');
    }

    // gradebook evaluation format items
    function create_format($evaluation_format)
    {
        return $this->create($evaluation_format);
    }

    function update_format($evaluation_format)
    {
        $condition = new EqualityCondition(Format :: PROPERTY_ID, $evaluation_format->get_id());
        return $this->update($evaluation_format, $condition);
    }

    function retrieve_all_active_evaluation_formats()
    {
        $condition = new EqualityCondition(Format :: PROPERTY_ACTIVE, Format :: EVALUATION_FORMAT_ACTIVE);
        return $this->retrieve_objects(Format :: get_table_name(), $condition, null, Format :: CLASS_NAME);
    }

    function retrieve_evaluation_formats($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Format :: get_table_name(), $condition, $offset, $max_objects, $order_by, Format :: CLASS_NAME);
    }

    function count_evaluation_formats()
    {
        return $this->count_objects(Format :: get_table_name());
    }

    function retrieve_evaluation_format($id)
    {
        $condition = new EqualityCondition(Format :: PROPERTY_ID, $id);
        return $this->retrieve_object(Format :: get_table_name(), $condition, null, Format :: CLASS_NAME);
    }

    // gradebook internal item
    function create_internal_item($internal_item)
    {
        return $this->create($internal_item);
    }

    function retrieve_internal_item_by_publication($application, $publication_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $application);
        $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_PUBLICATION_ID, $publication_id);
        $condition = new AndCondition($conditions);
        return $this->retrieve_object(InternalItem :: get_table_name(), $condition, null, InternalItem :: CLASS_NAME);
    }

    function retrieve_internal_item($id)
    {
        $condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $id);
        return $this->retrieve_object(InternalItem :: get_table_name(), $condition, null, InternalItem :: CLASS_NAME);
    }

    function retrieve_internal_item_by_internal_item_instance($internal_item_instance_id)
    {
        $condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $internal_item_instance_id);
        return $this->retrieve_object(InternalItem :: get_table_name(), $condition, null, InternalItem :: CLASS_NAME);
    }

    function delete_internal_item($internal_item)
    {
        $condition = new EqualityCondition(InternalItem :: PROPERTY_ID, $internal_item->get_id());
        return $this->delete(InternalItem :: get_table_name(), $condition, null, InternalItem :: CLASS_NAME);
    }

    function retrieve_categories_by_application($application)
    {
        $condition = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $application);
        return $this->retrieve_distinct(InternalItem :: get_table_name(), InternalItem :: PROPERTY_CATEGORY, $condition);
    }

    // internal item instance


    function delete_internal_item_instance($internal_item_instance)
    {
        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_ID, $internal_item_instance->get_id());
        return $this->delete(InternalItemInstance :: get_table_name(), $condition, null, InternalItemInstance :: CLASS_NAME);
    }

    function retrieve_evaluation_ids_by_internal_item_id($internal_item_id)
    {
        $internal_item_instance_alias = $this->get_alias(InternalItemInstance :: get_table_name());

        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $internal_item_id);
        $query = 'SELECT ' . $this->escape_column_name(InternalItemInstance :: PROPERTY_EVALUATION_ID, $internal_item_instance_alias) . ' FROM ' . $this->escape_table_name(InternalItemInstance :: get_table_name()) . ' AS ' . $internal_item_instance_alias;
        return $this->retrieve_record_set($query, InternalItemInstance :: get_table_name(), $condition);
    }

    function retrieve_internal_item_instance_by_evaluation($evaluation_id)
    {
        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_EVALUATION_ID, $evaluation_id);
        return $this->retrieve_object(InternalItemInstance :: get_table_name(), $condition, null, InternalItemInstance :: CLASS_NAME);
    }

    function retrieve_internal_item_instance($condition)
    {
        return $this->retrieve_object(InternalItemInstance :: get_table_name(), $condition, null, InternalItemInstance :: CLASS_NAME);
    }

    function count_internal_item_instance($condition)
    {
        return $this->count_objects(InternalItemInstance :: get_table_name(), $condition);
    }

    function create_internal_item_instance($internal_item_instance)
    {
        return $this->create($internal_item_instance);
    }

    function update_internal_item_instance($internal_item_instance)
    {
        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_ID, $internal_item_instance->get_id());
        return $this->update($internal_item_instance, $condition);
    }

    // gradebook evaluation


    function create_evaluation($evaluation)
    {
        return $this->create($evaluation);
    }

    function retrieve_all_evaluations_on_internal_publication($application, $publication_id, $offset = null, $max_objects = null, $order_by = null)
    {
        $udm = UserDataManager :: get_instance();

        $gradebook_evaluation_alias = $this->get_alias(Evaluation :: get_table_name());
        $gradebook_internal_item_alias = $this->get_alias(InternalItem :: get_table_name());
        $gradebook_internal_item_instance_alias = $this->get_alias(InternalItemInstance :: get_table_name());
        $gradebook_grade_evaluation_alias = $this->get_alias(GradeEvaluation :: get_table_name());
        $user_alias = $this->get_alias(User :: get_table_name());
        $user_evaluator_alias = $this->get_alias(User :: get_table_name()) . '2';
        $gradebook_format_alias = $this->get_alias(Format :: get_table_name());

        $query = 'SELECT ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_EVALUATOR_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_EVALUATION_DATE) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID);
        $query .= ', CONCAT(' . $user_alias . '.' . $this->escape_column_name(User :: PROPERTY_LASTNAME) . ', " ",' . $user_alias . '.' . $this->escape_column_name(User :: PROPERTY_FIRSTNAME) . ') AS user';
        $query .= ', CONCAT(' . $user_evaluator_alias . '.' . $this->escape_column_name(User :: PROPERTY_LASTNAME) . ', " ",' . $user_evaluator_alias . '.' . $this->escape_column_name(User :: PROPERTY_FIRSTNAME) . ') AS evaluator';
        $query .= ', ' . $gradebook_format_alias . '.' . $this->escape_column_name(Format :: PROPERTY_TITLE);
        $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->escape_column_name(GradeEvaluation :: PROPERTY_SCORE);
        $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->escape_column_name(GradeEvaluation :: PROPERTY_COMMENT);
        $query .= ' FROM ' . $this->escape_table_name(InternalItem :: get_table_name()) . ' AS ' . $gradebook_internal_item_alias;
        $query .= ' JOIN ' . $this->escape_table_name(InternalItemInstance :: get_table_name()) . ' AS ' . $gradebook_internal_item_instance_alias . ' ON ' . $this->escape_column_name(InternalItem :: PROPERTY_ID, $gradebook_internal_item_alias) . ' = ' . $this->escape_column_name(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $gradebook_internal_item_instance_alias);
        $query .= ' JOIN ' . $this->escape_table_name(Evaluation :: get_table_name()) . ' AS ' . $gradebook_evaluation_alias . ' ON ' . $this->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias) . ' = ' . $this->escape_column_name(InternalItemInstance :: PROPERTY_EVALUATION_ID, $gradebook_internal_item_instance_alias);
        $query .= ' JOIN ' . $this->escape_table_name(GradeEvaluation :: get_table_name()) . ' AS ' . $gradebook_grade_evaluation_alias . ' ON ' . $this->escape_column_name(GradeEvaluation :: PROPERTY_ID, $gradebook_grade_evaluation_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias);
        $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $udm->escape_column_name(User :: PROPERTY_ID, $user_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_USER_ID, $gradebook_evaluation_alias);
        $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $user_evaluator_alias . ' ON ' . $udm->escape_column_name(User :: PROPERTY_ID, $user_evaluator_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_EVALUATOR_ID, $gradebook_evaluation_alias);
        $query .= ' JOIN ' . $this->escape_table_name(Format :: get_table_name()) . ' AS ' . $gradebook_format_alias . ' ON ' . $this->escape_column_name(Format :: PROPERTY_ID, $gradebook_format_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID, $gradebook_evaluation_alias);

        $conditions = array();
        $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_PUBLICATION_ID, $publication_id, InternalItem :: get_table_name());
        $conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $application, InternalItem :: get_table_name());
        $condition = new AndCondition($conditions);
        return $this->retrieve_object_set($query, Evaluation :: get_table_name(), $condition, $offset, $max_objects, $order_by, Evaluation :: CLASS_NAME);
    }

    function retrieve_all_evaluations_on_external_publication($condition, $offset = null, $max_objects = null, $order_by = null)
    {
        $udm = UserDataManager :: get_instance();

        $gradebook_evaluation_alias = $this->get_alias(Evaluation :: get_table_name());
        $gradebook_external_item_alias = $this->get_alias(ExternalItem :: get_table_name());
        $gradebook_external_item_instance_alias = $this->get_alias(ExternalItemInstance :: get_table_name());
        $gradebook_grade_evaluation_alias = $this->get_alias(GradeEvaluation :: get_table_name());
        $evaluator_alias = $this->get_alias(User :: get_table_name());
        $user_alias = $this->get_alias(User :: get_table_name());
        $gradebook_format_alias = $this->get_alias(Format :: get_table_name());

        $query = 'SELECT ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_EVALUATION_DATE) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_EVALUATOR_ID) . ', ' . $gradebook_evaluation_alias . '.' . $this->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID);
        $query .= ', CONCAT(' . $user_alias . '.' . $this->escape_column_name(User :: PROPERTY_LASTNAME) . ', " ",' . $user_alias . '.' . $this->escape_column_name(User :: PROPERTY_FIRSTNAME) . ') AS evaluator';
        $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->escape_column_name(GradeEvaluation :: PROPERTY_SCORE);
        $query .= ', ' . $gradebook_grade_evaluation_alias . '.' . $this->escape_column_name(GradeEvaluation :: PROPERTY_COMMENT);
        $query .= ', ' . $gradebook_format_alias . '.' . $this->escape_column_name(Format :: PROPERTY_TITLE);
        $query .= ' FROM ' . $this->escape_table_name(ExternalItem :: get_table_name()) . ' AS ' . $gradebook_external_item_alias;
        $query .= ' JOIN ' . $this->escape_table_name(ExternalItemInstance :: get_table_name()) . ' AS ' . $gradebook_external_item_instance_alias . ' ON ' . $this->escape_column_name(ExternalItem :: PROPERTY_ID, $gradebook_external_item_alias) . ' = ' . $this->escape_column_name(ExternalItemInstance :: PROPERTY_EXTERNAL_ITEM_ID, $gradebook_external_item_instance_alias);
        $query .= ' JOIN ' . $this->escape_table_name(Evaluation :: get_table_name()) . ' AS ' . $gradebook_evaluation_alias . ' ON ' . $this->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias) . ' = ' . $this->escape_column_name(ExternalItemInstance :: PROPERTY_EVALUATION_ID, $gradebook_external_item_instance_alias);
        $query .= ' JOIN ' . $this->escape_table_name(GradeEvaluation :: get_table_name()) . ' AS ' . $gradebook_grade_evaluation_alias . ' ON ' . $this->escape_column_name(GradeEvaluation :: PROPERTY_ID, $gradebook_grade_evaluation_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_ID, $gradebook_evaluation_alias);
        $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $udm->escape_column_name(User :: PROPERTY_ID, $user_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_USER_ID, $gradebook_evaluation_alias);
        $query .= ' JOIN ' . $this->escape_table_name(Format :: get_table_name()) . ' AS ' . $gradebook_format_alias . ' ON ' . $this->escape_column_name(Format :: PROPERTY_ID, $gradebook_format_alias) . ' = ' . $this->escape_column_name(Evaluation :: PROPERTY_FORMAT_ID, $gradebook_evaluation_alias);

        return $this->retrieve_object_set($query, Evaluation :: get_table_name(), $condition, $offset, $max_objects, $order_by, Evaluation :: CLASS_NAME);
    }

    function retrieve_evaluation_ids_by_publication($application, $publication_id)
    {
        $internal_item = $this->retrieve_internal_item_by_publication($application, $publication_id);
        if (! $internal_item)
            return false;
        return $this->retrieve_evaluation_ids_by_internal_item_id($internal_item->get_id());
    }

    function count_all_evaluations_on_publication($publication_id)
    {
        $udm = UserDataManager :: get_instance();
        $gradebook_internal_item_instance_alias = $this->get_alias(InternalItemInstance :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(InternalItemInstance :: get_table_name()) . ' AS ' . $gradebook_internal_item_instance_alias;
        $internal_item = $this->retrieve_internal_item_by_publication(Request :: get('application'), $publication_id);
        $condition = new EqualityCondition(InternalItemInstance :: PROPERTY_INTERNAL_ITEM_ID, $internal_item->get_id());
        return $this->count_result_set($query, InternalItemInstance :: get_table_name(), $condition);
    }

    function delete_evaluation($evaluation)
    {
        $condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $evaluation->get_id());
        return $this->delete($evaluation->get_table_name(), $condition);
    }

    function update_evaluation($evaluation)
    {
        $condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $evaluation->get_id());
        return $this->update($evaluation, $condition);
    }

    function retrieve_evaluation($id)
    {
        $condition = new EqualityCondition(Evaluation :: PROPERTY_ID, $id);
        return $this->retrieve_object(Evaluation :: get_table_name(), $condition, null, Evaluation :: CLASS_NAME);
    }

    function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(Evaluation :: get_table_name(), $condition, $offset, $count, $order_property, Evaluation :: CLASS_NAME);
    }

    function create_evaluation_object_from_data($content_object_publication, $user, $date)
    {
        $evaluation = new Evaluation();
        $evaluation->set_evaluator_id($content_object_publication->get_publisher_user_id());
        $evaluation->set_user_id($user);
        $evaluation->set_evaluation_date($date);
        $evaluation->set_format_id($this->retrieve_format_id_by_format_name('percentage'));
        if ($this->create($evaluation))
            return $evaluation;
        return false;
    }

    // evaluation format


    function retrieve_format_id_by_format_name($format_name)
    {
        $condition = new EqualityCondition(Format :: PROPERTY_TITLE, $format_name);
        return $this->retrieve_object(Format :: get_table_name(), $condition, null, Format :: CLASS_NAME)->get_id();
    }

    //gradebook grade evaluation
    function create_grade_evaluation($grade_evaluation)
    {
        return $this->create($grade_evaluation, false);
    }

    function retrieve_grade_evaluation($condition)
    {
        return $this->retrieve_object(GradeEvaluation :: get_table_name(), $condition, null, GradeEvaluation :: CLASS_NAME);
    }

    function delete_grade_evaluation($grade_evaluation)
    {
        $condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $grade_evaluation->get_id());
        return $this->delete(GradeEvaluation :: get_table_name(), $condition);
    }

    function update_grade_evaluation($grade_evaluation)
    {
        $condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $grade_evaluation->get_id());
        return $this->update($grade_evaluation, $condition);
    }

    function create_grade_evaluation_object_from_data($tracker_score)
    {
        $grade_evaluation = new GradeEvaluation();
        $grade_evaluation->set_score($tracker_score);
        $grade_evaluation->set_comment('automatic generated result');
        if ($this->create($grade_evaluation))
            return $grade_evaluation;
        return false;
    }

    //gradebook external item


    function create_external_item($external_item)
    {
        return $this->create($external_item);
    }

    function delete_external_item($external_item)
    {
        $condition = new EqualityCondition(ExternalItem :: PROPERTY_ID, $external_item->get_id());
        return $this->delete(ExternalItem :: get_table_name(), $condition);
    }

    function create_external_item_by_content_object($content_object_id, $category)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $content_object = $rdm->retrieve_content_object($content_object_id);
        $external_item = new ExternalItem();
        $external_item->set_title($content_object->get_title());
        $external_item->set_description($content_object->get_description());
        $external_item->set_category($category);
        if ($this->create($external_item));
        return $external_item;
        return false;
    }

    function retrieve_external_items($condition, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ExternalItem :: get_table_name(), $condition, $offset, $count, $order_property, ExternalItem :: CLASS_NAME);
    }

    function retrieve_external_item($id)
    {
        $condition = new EqualityCondition(ExternalItem :: PROPERTY_ID, $id);
        return $this->retrieve_object(ExternalItem :: get_table_name(), $condition, null, ExternalItem :: CLASS_NAME);
    }

    function count_external_items($condition)
    {
        return $this->count_objects(ExternalItem :: get_table_name(), $condition);
    }

    //gradebook external item instance


    function create_external_item_instance($external_item_intance)
    {
        return $this->create($external_item_intance);
    }

    function count_external_item_instance($condition)
    {
        return $this->count_objects(ExternalItemInstance :: get_table_name(), $condition);
    }

    function delete_external_item_instance($external_item_instance)
    {
        $condition = new EqualityCondition(ExternalItemInstance :: PROPERTY_ID, $external_item_instance->get_id());
        return $this->delete($external_item_instance->get_table_name(), $condition);
    }

    function create_external_item_instance_by_moving($external_item, $evaluations_id)
    {
        if (is_array($evaluations_id))
        {
            for($i = 0; $i < count($evaluations_id); $i ++)
            {
                $id = $evaluations_id[$i]['evaluation_id'];
                if (! $this->create_external_item_instance_function($external_item, $id))
                    return false;
            }
        }
        else
        {
            return $this->create_external_item_instance_function($external_item, $evaluations_id);
        }
    }

    function create_external_item_instance_function($external_item, $id)
    {

        $external_item_instance = new ExternalItemInstance();
        $external_item_instance->set_external_item_id($external_item->get_id());
        $external_item_instance->set_evaluation_id($id);
        if ($this->create($external_item_instance))
        {
            if ($this->retrieve_internal_item_instance_by_evaluation($id))
            {
                if (! $this->delete_internal_item_instance($this->retrieve_internal_item_instance_by_evaluation($id)))
                    return false;
            }
        }
        else
            return false;
        return true;
    }

    function retrieve_external_item_instance($condition)
    {
        return $this->retrieve_object(ExternalItemInstance :: get_table_name(), $condition, null, ExternalItemInstance :: CLASS_NAME);
    }

    function retrieve_external_item_instances($condition)
    {
        return $this->retrieve_objects(ExternalItemInstance :: get_table_name(), $condition, null, null, null, ExternalItemInstance :: CLASS_NAME);
    }

    // applications
    function retrieve_internal_item_applications()
    {
        return $this->retrieve_distinct(InternalItem :: get_table_name(), InternalItem :: PROPERTY_APPLICATION);
    }

    function retrieve_calculated_internal_items()
    {
        $condition = new EqualityCondition(InternalItem :: PROPERTY_CALCULATED, 1);
        return $this->retrieve_distinct(InternalItem :: get_table_name(), InternalItem :: PROPERTY_ID, $condition);
    }

    function retrieve_internal_items_by_application($condition, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(InternalItem :: get_table_name(), $condition, $offset, $count, $order_property, InternalItem :: CLASS_NAME);
    }

    function count_internal_items_by_application($condition)
    {
        return $this->count_objects(InternalItem :: get_table_name(), $condition, null, InternalItem :: CLASS_NAME);
    }
}
?>
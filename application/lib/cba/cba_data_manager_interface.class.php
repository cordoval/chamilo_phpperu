<?php
interface CbaDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    // functions: Competency
    function get_next_competency_id();

    function create_competency($competency);

    function update_competency($competency);

    function delete_competency($competency);

    function count_competencys($conditions = null);

    function retrieve_competency($id);

    function retrieve_competencys($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_competency_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function count_competency_categories($conditions = null);

    // functions: Indicator
    function get_next_indicator_id();

    function create_indicator($indicator);

    function update_indicator($indicator);

    function delete_indicator($indicator);

    function count_indicators($conditions = null);

    function retrieve_indicator($id);

    function retrieve_indicators($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_indicator_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function count_indicator_categories($conditions = null);

    // functions: Criteria
    function get_next_criteria_id();

    function create_criteria($criteria);

    function update_criteria($criteria);

    function delete_criteria($criteria);

    function count_criterias($conditions = null);

    function retrieve_criteria($id);

    function retrieve_criterias($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_criteria_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function count_criteria_categories($conditions = null);

    // functions: CriteriaScore
    function get_next_criteria_score_id();

    function create_criteria_score($criteria_score);

    function update_criteria_score($criteria_score);

    function delete_criteria_score($criteria_score);

    function count_criterias_score($conditions = null);

    function retrieve_criteria_score($id);

    function retrieve_criteria_score_unique($id, $criteria_id);

    function retrieve_criterias_score($condition = null, $offset = null, $count = null, $order_property = null);

    // functions: CompetencyIndicator
    function get_next_competency_indicator_id();

    function create_competency_indicator($competency_indicator);

    function update_competency_indicator($competency_indicator);

    function delete_competency_indicator($competency_indicator);

    function count_competencys_indicator($conditions = null);

    function retrieve_competency_indicator($id);

    function retrieve_competencys_indicator($condition = null, $offset = null, $count = null, $order_property = null);

    // functions: IndicatorCriteria
    function get_next_indicator_criteria_id();

    function create_indicator_criteria($indicator_criteria);

    function update_indicator_criteria($indicator_criteria);

    function delete_indicator_criteria($indicator_criteria);

    function count_indicators_criteria($conditions = null);

    function retrieve_indicator_criteria($id);

    function retrieve_indicators_criteria($condition = null, $offset = null, $count = null, $order_property = null);

}
?>
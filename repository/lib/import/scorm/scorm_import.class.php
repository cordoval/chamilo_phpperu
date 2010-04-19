<?php
/**
 * $Id: scorm_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.scorm
 */

/**
 * Imports SCORM activities to learning paths
 */
class ScormImport extends ContentObjectImport
{

    function ScormImport($content_object_file, $user, $category)
    {
        parent :: __construct($content_object_file, $user, $category);
    }

    /**
     * Extract the xml file to an array
     *
     * @param String $file - Path to the file
     * @return Array of Strings - The xml output
     */
    private function extract_xml_file($file)
    {
        $options = array('forceEnum' => array('item', 'organization', 'resource', 'file', 'dependency', 'adlnav:hideLMSUI', 'imsss:preConditionRule', 'imsss:postConditionRule', 'imsss:exitConditionRule', 'imsss:ruleCondition', 'imsss:objective', 'imsss:sequencing'));
        return Utilities :: extract_xml_file($file, $options);
    }

    /**
     * Import the learning path(s) into the system
     *
     */
    public function import_content_object()
    {
        // Extract the zip file to the temporary directory
        $zip = Filecompression :: factory();
        $extracted_files_dir = $zip->extract_file($this->get_content_object_file_property('tmp_name'));
        
        // Extract the xml file to an array
        $manifest_file = $extracted_files_dir . '/imsmanifest.xml';
        
        if(!file_exists($manifest_file))
        {
        	Filesystem :: remove($extracted_files_dir);
        	return false;
        }
        
        $xml_data = $this->extract_xml_file($manifest_file);
        
        // Move content from zip file to files/scorm/{user_id}/{scorm_package_identifier}/
        $scorm_path = Path :: get(SYS_SCORM_PATH) . $this->get_user()->get_id() . '/' . $xml_data['identifier'] . '/';
        Filesystem :: move_file($extracted_files_dir, $scorm_path);
        
        // Read through the resources list and determine the correct paths
        $base_path = $xml_data['xml:base'];
        $resources_base_path = $xml_data['resources']['xml:base'];
        $resources_path = $this->get_user()->get_id() . '/' . $xml_data['identifier'] . '/' . $base_path . $resources_base_path;
        $resources_list = $this->build_resources_list($xml_data['resources']['resource'], $resources_path);
        
        $sequencing_collections = $xml_data['imsss:sequencingCollection'];
        
        $version = $xml_data['version'];
        if(!$version)
        {
        	$version = $xml_data['metadata']['schemaversion'];	
        }
        
        $version = 'SCORM' . $version;
        
        // Build the organizations tree
        $learning_paths = $this->build_organizations($xml_data['organizations']['organization'], $resources_list, $sequencing_collections, $version, $xml_data['identifier']);
        //dump($xml_data);
        // Remove the temporary files
        Filesystem :: remove($extracted_files_dir);
        
        return $learning_paths;
    }

    /**
     * Build the resources list from the SCORM resources. 
     * This method will be used to place the correct path in the scorm items
     *
     * @param Array of Strings $resources - SCORM resources
     * @return Array of Strings - The needed resources
     */
    private function build_resources_list($resources, $resources_path)
    {
        $resources_list = array();
        
        foreach ($resources as $resource)
        {
            if ($resource['href'])
                $resources_list[$resource['identifier']] = $resources_path . $resource['xml:base'] . $resource['href'];
        }
        
        return $resources_list;
    }

    /*
	 * Build the learning path list from the SCORM organizations
	 * @param Array of Strings - SCORM organizations
	 */
    private function build_organizations($organizations, $resources_list, $sequencing_collections, $version, $path = null)
    {
        $learning_paths = array();
        
        foreach ($organizations as $organization)
        {
            $learning_path = $this->create_learning_path($organization, $sequencing_collections, $version, $path);
            $this->build_items($organization['item'], $resources_list, $learning_path, $sequencing_collections, $version);
            $learning_paths[] = $learning_path;
        }
        
        return $learning_paths;
    }

    /**
     * Recursive method to built the items list. When child items are found, a sub learning path has to be created
     * and the children must be processed
     *
     * @param Array of Strings $items - The items list
     * @param Array of Strings $resources_list - The resources list
     * @param LearningPath $parent_learning_path - The parent learning path
     */
    private function build_items($items, $resources_list, $parent_learning_path, $sequencing_collections, $version)
    {
        foreach ($items as $item)
        {
            if ($item['item'])
            {
                $sub_learning_path = $this->create_learning_path($item, $sequencing_collections, $version);
                $this->build_items($item['item'], $resources_list, $sub_learning_path, $sequencing_collections);
                $this->add_sub_learning_path_to_learning_path($parent_learning_path, $sub_learning_path);
            }
            else
            {
                $scorm_item = $this->create_scorm_item($item, $resources_list[$item['identifierref']], $sequencing_collections);
                $this->add_scorm_item_to_learning_path($scorm_item, $parent_learning_path, $item);
            }
        }
    }

    // Learning Object Methods
    

    /**
     * Creates a learning path from a title
     *
     * @param String $title
     * @return LearningPath
     */
    private function create_learning_path($item, $sequencing_collections, $version, $path = null)
    {
        $title = $item['title'];
        $learning_path = AbstractContentObject :: factory(LearningPath :: get_type_name());
        $learning_path->set_title($title);
        $learning_path->set_description($title);
        $learning_path->set_parent_id($this->get_category());
        $learning_path->set_owner_id($this->get_user()->get_id());
        $learning_path->set_version($version);
        
        if ($path)
            $learning_path->set_path($path);
        
        $lp_sequencing = $item['imsss:sequencing'][0];
        
        //$lp_sequencing = array_filter($lp_sequencing, array($this, "remove_null_values"));
        

        $global_sequencing = array();
        foreach ($sequencing_collections['imsss:sequencing'] as $sequencing)
        {
            if ($sequencing['ID'] == $lp_sequencing['IDRef'])
            {
                $global_sequencing = $sequencing;
            }
        }
        
        $sequencing = array_merge($global_sequencing, $lp_sequencing);
        
        if ($sequencing['imsss:controlMode'])
            $learning_path->set_control_mode($sequencing['imsss:controlMode']);
        
        $learning_path->create();
        
        return $learning_path;
    }

    function remove_null_values($var)
    {
        return $var != null;
    }

    /**
     * Creates a SCORM item from an item tag in the imsmanifest.xml
     *
     * @param Array[String] $item
     * @return ScormItem
     */
    private function create_scorm_item($item, $path, $sequencing_collections)
    {
        $scorm_item = AbstractContentObject :: factory('scorm_item');
        $scorm_item->set_title($item['title']);
        $scorm_item->set_description($item['title']);
        $scorm_item->set_parent_id($this->get_category());
        $scorm_item->set_owner_id($this->get_user()->get_id());
        $scorm_item->set_path($path);
        $scorm_item->set_identifier($item['identifier']);
        
        if ($item['isvisible'])
            $scorm_item->set_visible(($item['isvisible'] == 'true'));
        
        if ($item['parameters'])
            $scorm_item->set_parameters($item['parameters']);
        
        if ($item['adlcp:completionTreshold'])
            $scorm_item->set_completion_treshold($item['adlcp:completionTreshold']);
        
        if ($item['adlcp:dataFromLMS'])
            $scorm_item->set_data_from_lms($item['adlcp:dataFromLMS']);
            
        //SCORM1.2
        if ($item['adlcp:datafromlms'])
            $scorm_item->set_data_from_lms($item['adlcp:datafromlms']);
        
        if ($item['adlcp:timeLimitAction'])
            $scorm_item->set_time_limit_action($item['adlcp:timeLimitAction']);
            
        //SCORM1.2		
        if ($item['adlcp:timelimitaction'])
            $scorm_item->set_time_limit_action($item['adlcp:timelimitaction']);
            
        //SCORM1.2
        if ($item['adlcp:maxtimeallowed'])
            $scorm_item->set_time_limit($item['adlcp:maxtimeallowed']);
            
        //SCORM1.2
        if ($item['adlcp:masteryscore'])
            $scorm_item->set_mastery_score($item['adlcp:masteryscore']);
            
        //SCORM1.2 
        if ($item['adlcp:prerequisites']['_content'])
            $scorm_item->set_prerequisites($item['adlcp:prerequisites']['_content']);
        
        $hideLMSUI = $item['adlnav:presentation']['adlnav:navigationInterface']['adlnav:hideLMSUI'];
        if ($hideLMSUI)
            $scorm_item->set_hide_lms_ui($hideLMSUI);
            
        //Sequencing;
        $item_sequencing = $item['imsss:sequencing'][0];
        
        if ($item_sequencing)
        {
            //$item_sequencing = array_filter($item_sequencing, array($this, "remove_null_values"));
            

            $global_sequencing = array();
            foreach ($sequencing_collections['imsss:sequencing'] as $sequencing)
            {
                if ($sequencing['ID'] == $item_sequencing['IDRef'])
                {
                    $global_sequencing = $sequencing;
                }
            }
            
            $sequencing = array_merge($global_sequencing, $item_sequencing);
            
            if ($sequencing['imsss:controlMode'])
                $scorm_item->set_control_mode($sequencing['imsss:controlMode']);
            
            $limit_conditions = $sequencing['imsss:limitConditions'];
            if ($limit_conditions['attemptAbsoluteDurationLimit'])
                $scorm_item->set_time_limit($limit_conditions['attemptAbsoluteDurationLimit']);
            
            $objectives = $sequencing['imsss:objectives'];
            
            $primary_objective = $objectives['imsss:primaryObjective'];
            
            if ($primary_objective)
            {
                $objective = new Objective();
                
                if ($primary_objective['objectiveID'])
                    $objective->set_id($primary_objective['objectiveID']);
                
                if ($primary_objective['satisfiedByMeasure'])
                    $objective->set_satisfied_by_measure($primary_objective['satisfiedByMeasure']);
                
                if ($primary_objective['imsss:minNormalizedMeasure'])
                    $objective->set_minimum_satisfied_measure($primary_objective['imsss:minNormalizedMeasure']);
                
                $objective->set_contributes_to_rollup(1);
                
                $scorm_item->add_objective($objective, true);
            }
            
            $other_objectives = $objectives['imsss:objective'];
            foreach ($other_objectives as $other_objective)
            {
                $objective = new Objective();
                
                if ($other_objective['objectiveID'])
                    $objective->set_id($other_objective['objectiveID']);
                
                if ($other_objective['satisfiedByMeasure'])
                    $objective->set_satisfied_by_measure($other_objective['satisfiedByMeasure']);
                
                if ($other_objective['imsss:minNormalizedMeasure'])
                    $objective->set_minimum_satisfied_measure($other_objective['imsss:minNormalizedMeasure']);
                
                $scorm_item->add_objective($objective, false);
            }
            
            $objective_set_by_content = $sequencing['imsss:deliveryControls']['objectiveSetByContent'];
            if ($objective_set_by_content == 'true')
                $scorm_item->set_objective_set_by_content(1);
            
            $sequencing_rules = $sequencing['imsss:sequencingRules'];
            $types = array('pre', 'post', 'exit');
            
            foreach ($types as $type)
            {
                $condition_rules = $sequencing_rules['imsss:' . $type . 'ConditionRule'];
                foreach ($condition_rules as $xml_condition_rule)
                {
                    $conditions = $xml_condition_rule['imsss:ruleConditions']['imsss:ruleCondition'];
                    $action = $xml_condition_rule['imsss:ruleAction']['action'];
                    
                    $condition_rule = new ConditionRule();
                    $condition_rule->set_action($action);
                    if ($xml_condition_rule['imsss:ruleConditions']['conditionCombination'])
                        $condition_rule->set_conditions_operator($xml_condition_rule['imsss:ruleConditions']['conditionCombination']);
                    
                    foreach ($conditions as $condition)
                    {
                        $cond = new RuleCondition();
                        $cond->set_condition($condition['condition']);
                        
                        if ($condition['operator'] == 'not')
                            $cond->set_not_condition(true);
                        
                        if ($condition['referencedObjective'])
                            $cond->set_referenced_objective($condition['referencedObjective']);
                        
                        $condition_rule->add_condition($cond);
                    }
                    
                    $scorm_item->add_condition_rule($condition_rule, $type);
                }
            }
        }
        
        $scorm_item->create();
        
        return $scorm_item;
    }

    /**
     * Adds a SCORM item to a learning path
     *
     * @param ScormItem $scorm_item
     * @param LearningPath $learning_path
     * @param Array String $item -> The item in xml form, needed for sequencing data
     * @return ComplexScormItem - The wrapper
     */
    private function add_scorm_item_to_learning_path($scorm_item, $learning_path, $item)
    {
        $learning_path_item = AbstractContentObject :: factory(LearningPathItem :: get_type_name());
        $learning_path_item->set_parent_id($this->get_category());
        $learning_path_item->set_owner_id($this->get_user()->get_id());
        $learning_path_item->set_title($scorm_item->get_title());
        $learning_path_item->set_description($scorm_item->get_description());
        $learning_path_item->set_reference($scorm_item->get_id());
        $learning_path_item->set_mastery_score($scorm_item->get_mastery_score());
        
        $limit_conditions = $item['imsss:sequencing']['imsss:limitConditions'];
        if ($limit_conditions['attemptLimit'])
            $learning_path_item->set_max_attempts($limit_conditions['attemptLimit']);
        
        $learning_path_item->create();
        
        $wrapper = ComplexContentObjectItem :: factory(LearningPathItem :: get_type_name());
        $wrapper->set_ref($learning_path_item->get_id());
        $wrapper->set_parent($learning_path->get_id());
        $wrapper->set_user_id($this->get_user()->get_id());
        $wrapper->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($learning_path->get_id()));
        $wrapper->set_prerequisites($scorm_item->get_prerequisites());
        $wrapper->create();
        
        return $wrapper;
    }

    /**
     * Adds a sub learning path to a learning path
     * @param LearningPath $sub_learning_path
     * @param LearningPath $learning_path
     * @return ComplexScormItem - The wrapper
     */
    private function add_sub_learning_path_to_learning_path($learning_path, $sub_learning_path)
    {
        $wrapper = ComplexContentObjectItem :: factory(LearningPath :: get_type_name());
        $wrapper->set_ref($sub_learning_path->get_id());
        $wrapper->set_parent($learning_path->get_id());
        $wrapper->set_user_id($this->get_user()->get_id());
        $wrapper->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($learning_path->get_id()));
        $wrapper->create();
        
        return $wrapper;
    }
}
?>
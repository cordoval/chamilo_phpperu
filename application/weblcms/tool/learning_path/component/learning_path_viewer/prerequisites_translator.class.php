<?php
/**
 * $Id: prerequisites_translator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer
 */
class PrerequisitesTranslator
{
    private $lpi_tracker_data;
    private $objects;
    private $items;
    private $version;

    function PrerequisitesTranslator($lpi_tracker_data, $objects, $version)
    {
        $this->lpi_tracker_data = $lpi_tracker_data;
        $this->objects = $objects;
        $this->version = $version;
    }

    function can_execute_item($item)
    {
        $prerequisites = $item->get_prerequisites();

        if ($prerequisites)
            $executable = $this->prerequisite_completed($prerequisites);
        else
            return true;
        
        return $executable;
    }

    function prerequisite_completed($prerequisites)
    {
        $matches = $items = array();
        $pattern = '/[^\(\)\&\|~]*/';
        preg_match_all($pattern, $prerequisites, $matches);
        rsort($matches[0], SORT_NUMERIC);
        
        foreach ($matches[0] as $match)
        {
            if ($match)
            {
                if (! in_array($match, $items))
                    $items[] = $match;
            }
        }
        
        foreach ($items as $item)
        {
        	if($item == -1) //if an empty box was selected, the prerequisite is automatically completed
        		$value = 1;
        		else{       		
		            if ($this->version == 'SCORM1.2')
		                $real_id = $this->retrieve_real_id_from_prerequisite_identifier($item);
		            else
		                $real_id = $item;
		
		            $value = 0;
		            
		            foreach ($this->lpi_tracker_data[$real_id]['trackers'] as $tracker_data)
		            {
		                if ($tracker_data->get_status() == 'completed' || $tracker_data->get_status() == 'passed')
		                {
		                    $value = 1;
		                    break;
		                }
            		}
        		}
            $prerequisites = str_replace($item, $value, $prerequisites);
        }
        $prerequisites = str_replace('&', '&&', $prerequisites);
        $prerequisites = str_replace('|', '||', $prerequisites);
        $prerequisites = str_replace('~', '!', $prerequisites);
        $prerequisites = '$value = ' . $prerequisites . ';';

        eval($prerequisites);
        
        return $value;
    }

    function retrieve_real_id_from_prerequisite_identifier($identifier)
    {
        foreach ($this->objects as $cid => $object)
        {
            if ($object->get_identifier() == $identifier)
                return $cid;
        }
        
        return - 1;
    }

}
?>
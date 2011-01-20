<?php
namespace repository\content_object\adaptive_assessment;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class PrerequisitesTranslator
{
    private $adaptive_assessment_item_attempt_data;
    private $content_objects;
    private $items;

    function __construct($adaptive_assessment_item_attempt_data, $content_objects)
    {
        $this->adaptive_assessment_item_attempt_data = $adaptive_assessment_item_attempt_data;
        $this->content_objects = $content_objects;
    }

    function can_execute_item($item)
    {
        $prerequisites = $item->get_prerequisites();

        if ($prerequisites)
        {
            $executable = $this->prerequisite_completed($prerequisites);
        }
        else
        {
            return true;
        }

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
                {
                    $items[] = $match;
                }
            }
        }

        foreach ($items as $item)
        {
            //if an empty box was selected, the prerequisite is automatically completed
            if ($item == - 1)
            {
                $value = 1;
            }
            else
            {
                $value = 0;

                foreach ($this->adaptive_assessment_item_attempt_data[$item]['trackers'] as $tracker_data)
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

        return eval($prerequisites);
    }

    function retrieve_real_id_from_prerequisite_identifier($identifier)
    {
        foreach ($this->content_objects as $cid => $content_object)
        {
            if ($content_object->get_identifier() == $identifier)
            {
                return $cid;
            }
        }

        return - 1;
    }

}
?>
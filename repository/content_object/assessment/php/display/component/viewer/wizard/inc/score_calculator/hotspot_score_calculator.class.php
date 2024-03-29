<?php
namespace repository\content_object\assessment;

use common\libraries\Path;
use PointInPolygon;

/**
 * $Id: hotspot_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';
require_once Path :: get_plugin_path() . 'polygon/point_in_polygon.class.php';

class HotspotScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        $answers = $question->get_answers();
        
        $score = 0;
        $total_weight = 0;
        
        foreach ($answers as $index => $answer)
        {
            $user_answer = $user_answers[$index];
            $hotspot_coordinates = $answer->get_hotspot_coordinates();
            
            $polygon = new PointInPolygon(unserialize($hotspot_coordinates));
            $is_inside = $polygon->is_inside(unserialize($user_answer));
            
            switch ($is_inside)
            {
                case PointInPolygon :: POINT_INSIDE :
                    $score += $answer->get_weight();
                    break;
                case PointInPolygon :: POINT_BOUNDARY :
                    $score += $answer->get_weight();
                    break;
                case PointInPolygon :: POINT_VERTEX :
                    $score += $answer->get_weight();
                    break;
            }
            
            $total_weight += $answer->get_weight();
        }
        
        return $this->make_score_relative($score, $total_weight);
    }
}
?>
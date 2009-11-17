<?php
/**
 * $Id: hotspot_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.result_viewer.survey_question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';
require_once Path :: get_plugin_path() . 'polygon/point_in_polygon.class.php';

class HotspotQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $question = $this->get_question();
        $question_id = $this->get_clo_question()->get_id();
        $answers = $question->get_answers();
        
        $image_object = $question->get_image_object();
        $dimensions = getimagesize($image_object->get_full_path());
        
        $html[] = '<div style="border: 1px solid #B5CAE7; border-top: none; padding: 10px;">';
        
        $html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' . $question_id . '" class="hotspot_image" style="width: ' . $dimensions[0] . 'px; height: ' . $dimensions[1] . 'px; background-image: url(' . $image_object->get_url() . ')"></div></div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question_result_display.js');
        
        $html[] = '<div class="clear"></div></div>';
        
        $user_answers = $this->get_answers();
        $colors = array('#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff');
        
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><div class="colour_box" style="background-color: ' . $colors[$i] . ';"></div></td>';
            $html[] = '<td>' . ($this->is_valid_answer($answer, $user_answers[$i]) ? Theme :: get_common_image('action_confirm') : Theme :: get_common_image('action_delete')) . '</td>';
            $html[] = '<td>' . $answer->get_answer() . '</td>';
            $html[] = '<input type="hidden" name="coordinates_' . $this->get_clo_question()->get_id() . '_' . $i . '" value="' . $answer->get_hotspot_coordinates() . '" />';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
    }

    function is_valid_answer($answer, $user_answer)
    {
        $hotspot_coordinates = $answer->get_hotspot_coordinates();
        
        $polygon = new PointInPolygon(unserialize($hotspot_coordinates));
        $is_inside = $polygon->is_inside(unserialize($user_answer));
        
        switch ($is_inside)
        {
            case PointInPolygon :: POINT_INSIDE :
                return true;
            case PointInPolygon :: POINT_BOUNDARY :
                return true;
            case PointInPolygon :: POINT_VERTEX :
                return true;
        }
        
        return false;
    }

}
?>
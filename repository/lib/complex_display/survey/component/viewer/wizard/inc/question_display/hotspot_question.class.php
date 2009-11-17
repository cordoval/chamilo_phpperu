<?php
/**
 * $Id: hotspot_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
class HotspotQuestionDisplay extends QuestionDisplay
{
    //private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893');
    private $colours = array('#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff');

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        $answers = $this->shuffle_with_keys($question->get_answers());
        $renderer = $this->get_renderer();
        
        $question_id = $clo_question->get_id();
        
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js'));
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js'));
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question_display.js'));
        
        $image_html = array();
        $image_object = $question->get_image_object();
        $dimensions = getimagesize($image_object->get_full_path());
        $image_html[] = '<div class="description_hotspot">';
        $image_html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' . $question_id . '" class="hotspot_image" style="width: ' . $dimensions[0] . 'px; height: ' . $dimensions[1] . 'px; background-image: url(' . $image_object->get_url() . ')"></div></div>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '<div id="hotspot_marking_' . $question_id . '" class="hotspot_marking">';
        $image_html[] = '<div class="colour_box_label">' . Translation :: get('CurrentlyMarking') . '</div>';
        $image_html[] = '<div class="colour_box"></div>';
        $image_html[] = '<a href="#" class="button positive_button confirm_hotspot" style="display: none;">' . Translation :: get('ConfirmHotspotSelection') . '</a>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '</div>';
        $image_html[] = '<div class="clear"></div>';
        $image_html[] = '</div>';
        $formvalidator->addElement('html', implode("\n", $image_html));
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        foreach ($answers as $i => $answer)
        {
            $answer_name = $question_id . '_' . $i;
            
            $group = array();
            $group[] = $formvalidator->createElement('static', null, null, '<div class="colour_box" style="background-color: ' . $this->colours[$i] . ';"></div>');
            $group[] = $formvalidator->createElement('static', null, null, $answer->get_answer());
            $group[] = $formvalidator->createElement('static', null, null, '<img class="hotspot_configured" src="' . Theme :: get_common_image_path() . 'action_confirm.png" style="display: none;" /><img id="edit_' . $answer_name . '" class="edit_option" type="image" src="' . Theme :: get_common_image_path() . 'action_edit.png" />');
            $group[] = $formvalidator->createElement('static', null, null, '<img id="reset_' . $answer_name . '" class="reset_option" type="image" src="' . Theme :: get_common_image_path() . 'action_reset.png" />');
            $group[] = $formvalidator->createElement('hidden', $answer_name, '', 'class="hotspot_coordinates"');
            
            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            
            $renderer->setElementTemplate('<tr id="' . $answer_name . '" class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
        
    //		$this->add_scripts_element($clo_question->get_id(), $formvalidator);
    //		//$formvalidator->addElement('html', '<br/>');
    //		$answers = $question->get_answers();
    //		foreach ($answers as $i => $answer)
    //		{
    //			$formvalidator->addElement('hidden', $clo_question->get_id().'_'.$i, '', array('id' => $clo_question->get_id().'_'.$i));
    //		}
    }

    function get_instruction()
    {
        return Translation :: get('MarkHotspots');
    }
}
?>
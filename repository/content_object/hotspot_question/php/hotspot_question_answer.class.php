<?php
/**
 * $Id: hotspot_question_answer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotspot_question
 */
class HotspotQuestionAnswer
{
    private $answer;
    private $comment;
    private $weight;
    private $hotspot_coordinates;

    function HotSpotQuestionAnswer($answer, $comment, $weight, $coords)
    {
        $this->set_answer($answer);
        $this->set_comment($comment);
        $this->set_weight($weight);
        $this->set_hotspot_coordinates($coords);
    }

    function set_answer($answer)
    {
        $this->answer = $answer;
    }

    function set_comment($comment)
    {
        $this->comment = $comment;
    }

    function set_hotspot_coordinates($coords)
    {
        $this->hotspot_coordinates = $coords;
    }

    function set_weight($weight)
    {
        $this->weight = $weight;
    }

    function get_answer()
    {
        return $this->answer;
    }

    function get_comment()
    {
        return $this->comment;
    }

    function get_weight()
    {
        return $this->weight;
    }

    function get_hotspot_coordinates()
    {
        return $this->hotspot_coordinates;
    }

    function get_hotspot_type()
    {
        return $this->hotspot_type;
    }
}
?>
<?php
/**
 * $Id: objective.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.scorm_item.objectives
 */
class Objective
{
    private $id;
    private $satisfied_by_measure;
    private $minimum_satisfied_measure;
    private $contributes_to_rollup;

    function Objective($index)
    {
        $this->id = $index;
        $this->satisfied_by_measure = 0;
        $this->minimum_satisfied_measure = "1.0";
        $this->contributes_to_rollup = 0;
    }

    function get_id()
    {
        return $this->id;
    }

    function set_id($id)
    {
        $this->id = $id;
    }

    function get_satisfied_by_measure()
    {
        return $this->satisfied_by_measure;
    }

    function set_satisfied_by_measure($satisfied_by_measure)
    {
        $this->satisfied_by_measure = $satisfied_by_measure;
    }

    function get_minimum_satisfied_measure()
    {
        return $this->minimum_satisfied_measure;
    }

    function set_minimum_satisfied_measure($minimum_satisfied_measure)
    {
        $this->minimum_satisfied_measure = $minimum_satisfied_measure;
    }

    function get_contributes_to_rollup()
    {
        return $this->contributes_to_rollup;
    }

    function set_contributes_to_rollup($contributes_to_rollup)
    {
        $this->contributes_to_rollup = $contributes_to_rollup;
    }
}
?>
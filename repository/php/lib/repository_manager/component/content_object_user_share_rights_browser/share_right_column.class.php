<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * a column where the cells render a right
 *
 * @author Pieterjan Broekaert
 */
class ShareRightColumn extends StaticTableColumn
{
    private $right_id;

    function ShareRightColumn($title, $right_id)
    {
        $this->right_id = $right_id;
        parent :: StaticTableColumn($title);
    }

    function get_right_id()
    {
        return $this->right_id;
    }
}

?>

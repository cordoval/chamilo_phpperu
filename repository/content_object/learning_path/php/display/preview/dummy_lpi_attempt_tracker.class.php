<?php
namespace repository\content_object\learning_path;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use tracking\SimpleTracker;

/**
 * @package application.lib.weblcms.trackers
 */
class DummyLpiAttemptTracker
{
    private $lp_item_id;

    function get_id()
    {
        return 1;
    }

    function get_lp_view_id()
    {
        return 1;
    }

    function set_lp_view_id($lp_view_id)
    {
    }

    function get_start_time()
    {
        return time();
    }

    function set_start_time($start_time)
    {
    }

    function get_lp_item_id()
    {
        return $this->lp_item_id;
    }

    function set_lp_item_id($lp_item_id)
    {
        $this->lp_item_id = $lp_item_id;
    }

    function get_total_time()
    {
        return 0;
    }

    function set_total_time($total_time)
    {
    }

    function get_score()
    {
        return 0;
    }

    function set_score($score)
    {
    }

    function get_status()
    {
        return 'completed';
    }

    function set_status($status)
    {
    }

    function get_min_score()
    {
        return 0;
    }

    function set_min_score($min_score)
    {
    }

    function get_max_score()
    {
        return 0;
    }

    function set_max_score($max_score)
    {
    }

    function update()
    {
    }
}
?>
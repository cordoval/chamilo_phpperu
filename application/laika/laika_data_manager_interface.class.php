<?php
interface LaikaDataManagerInterface
{
    /**
     * Initializes the data manager.
     */
    function initialize();

    function retrieve_laika_question($id);

    function retrieve_laika_questions($condition = null, $offset = null, $count = null, $order_property = null);

    function create_laika_attempt($laika_attempt);

    function create_laika_answer($laika_answer);

    function create_laika_scale($laika_scale);

    function create_laika_result($laika_result);

    function create_laika_calculated_result($laika_calculated_result);

    function retrieve_laika_scale($id);

    function retrieve_laika_scales($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_laika_result($id);

    function retrieve_laika_results($condition = null, $offset = null, $count = null, $order_property = null);

    function has_taken_laika($user);

    function count_laika_attempts($condition = null);

    function count_laika_questions($condition = null);

    function count_laika_calculated_results($condition = null);

    function retrieve_laika_cluster($id);

    function retrieve_laika_clusters($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_laika_calculated_result($id);

    function retrieve_laika_calculated_results($condition = null, $offset = null, $count = null, $order_property = null);
}
?>
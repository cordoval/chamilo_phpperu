<?php
namespace repository\content_object\assessment;

class FeedbackDisplayConfiguration
{

    /**
     * Display feedback after every page
     * @var boolean
     */
    private $feedback_per_page;

    /**
     * Display feedback at the end
     * @var boolean
     */
    private $feedback_summary;

    /**
     * Type of feedback to give
     * @var int
     */
    private $feedback_type;

    const TYPE_BOTH = 0;
    const TYPE_TEXT = 1;
    const TYPE_NUMERIC = 2;

    /**
     * @param boolean $feedback_summary
     * @param boolean $feedback_per_page
     * @param int $feedback_type
     */
    function __construct($feedback_summary = true, $feedback_per_page = false, $feedback_type)
    {
        $this->feedback_summary = $feedback_summary;
        $this->feedback_per_page = $feedback_per_page;
        $this->feedback_type = $feedback_type;
    }

    /**
     * Disable feedback summary
     */
    function disable_feedback_summary()
    {
        $this->feedback_summary = false;
    }

    /**
     * Enable feedback summary
     */
    function enable_feedback_summary()
    {
        $this->feedback_summary = true;
    }

    /**
     * Disable feedback per page
     */
    function disable_feedback_per_page()
    {
        $this->feedback_per_page = false;
    }

    /**
     * Enable feedback per page
     */
    function enable_feedback_per_page()
    {
        $this->feedback_per_page = true;
    }

    /**
     * Set the feedback type
     * @param int $feedback_type
     */
    function set_feedback_type($feedback_type)
    {
        $this->feedback_type = $feedback_type;
    }

    /**
     * Get the feedback type
     * @return int
     */
    function get_feedback_type()
    {
        return $this->feedback_type;
    }

    /**
     * Get the feedback summary status
     * @return boolean
     */
    function get_feedback_summary()
    {
        return $this->feedback_summary;
    }

    /**
     * Get the feedback per page status
     * @return boolean
     */
    function get_feedback_per_page()
    {
        return $this->feedback_per_page;
    }

    /**
     * Display numeric feedback
     * @return boolean
     */
    function display_numeric_feedback()
    {
        return $this->get_feedback_type() == self :: TYPE_BOTH || $this->get_feedback_type() == self :: TYPE_NUMERIC;
    }

    /**
     * Display textual feedback
     * @return boolean
     */
    function display_textual_feedback()
    {
        return $this->get_feedback_type() == self :: TYPE_BOTH || $this->get_feedback_type() == self :: TYPE_TEXT;
    }
}
?>
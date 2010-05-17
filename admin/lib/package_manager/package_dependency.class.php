<?php
abstract class PackageDependency
{
    const PROPERTY_ID = 'id';
    const PROPERTY_SEVERITY = 'severity';

    const COMPARE_EQUAL = 1;
    const COMPARE_NOT_EQUAL = 2;
    const COMPARE_GREATER_THEN = 3;
    const COMPARE_GREATER_THEN_OR_EQUAL = 4;
    const COMPARE_LESS_THEN = 5;
    const COMPARE_LESS_THEN_OR_EQUAL = 6;

    const FAILURE_CRITICAL = 1;
    const FAILURE_HIGH = 2;
    const FAILURE_MEDIUM = 3;
    const FAILURE_LOW = 4;
    const FAILURE_VERY_LOW = 5;

    private $id;
    private $severity;
    private $message_logger;

    static function factory($type, $dependency)
    {
        $class = Utilities :: underscores_to_camelcase($type) . 'PackageDependency';
        require_once dirname(__FILE__) . '/dependency/' . $type . '.class.php';
        return new $class($dependency);
    }

    function PackageDependency($dependency)
    {
    	$this->set_id($dependency['id']);
    	$this->set_severity($dependency['severity']);
    	$this->message_logger = new MessageLogger();
    }

    function get_message_logger()
    {
        return $this->message_logger;
    }

    abstract function check();

    abstract function as_html();

    function get_id()
    {
        return $this->id;
    }

    /**
     * @return the $severity
     */
    public function get_severity()
    {
        return $this->severity;
    }

    /**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param $severity the $severity to set
     */
    public function set_severity($severity)
    {
        $this->severity = $severity;
    }

    function is_severe()
    {
        switch ($this->get_severity())
        {
            case self :: FAILURE_CRITICAL :
                return true;
                break;
            case self :: FAILURE_HIGH :
                return true;
                break;
            case self :: FAILURE_MEDIUM :
                return false;
                break;
            case self :: FAILURE_LOW :
                return false;
                break;
            case self :: FAILURE_VERY_LOW :
                return false;
                break;
            default :
                return true;
                break;
        }
    }

    function compare($type, $reference, $value)
    {
        switch ($type)
        {
            case self :: COMPARE_EQUAL :
                return ($reference == $value);
                break;
            case self :: COMPARE_NOT_EQUAL :
                return ($reference != $value);
                break;
            case self :: COMPARE_GREATER_THEN :
                return ($value > $reference);
                break;
            case self :: COMPARE_GREATER_THEN_OR_EQUAL :
                return ($value >= $reference);
                break;
            case self :: COMPARE_LESS_THEN :
                return ($value < $reference);
                break;
            case self :: COMPARE_LESS_THEN_OR_EQUAL :
                return ($value <= $reference);
                break;
            default :
                return false;
                break;
        }
    }

    function version_compare($type, $reference, $value)
    {
        switch ($type)
        {
            case self :: COMPARE_EQUAL :
                return version_compare($reference, $value, '==');
                break;
            case self :: COMPARE_NOT_EQUAL :
                return version_compare($reference, $value, '!=');
                break;
            case self :: COMPARE_GREATER_THEN :
                return version_compare($value, $reference, '>');
                break;
            case self :: COMPARE_GREATER_THEN_OR_EQUAL :
                return version_compare($value, $reference, '>=');
                break;
            case self :: COMPARE_LESS_THEN :
                return version_compare($value, $reference, '<');
                break;
            case self :: COMPARE_LESS_THEN_OR_EQUAL :
                return version_compare($value, $reference, '<=');
                break;
            default :
                return false;
                break;
        }
    }

}
?>
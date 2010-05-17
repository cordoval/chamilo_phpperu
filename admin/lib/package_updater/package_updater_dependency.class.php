<?php

abstract class PackageUpdaterDependency
{
    const FAILURE_CRITICAL = 1;
    const FAILURE_HIGH = 2;
    const FAILURE_MEDIUM = 3;
    const FAILURE_LOW = 4;
    const FAILURE_VERY_LOW = 5;
    
    const COMPARE_EQUAL = 1;
    const COMPARE_NOT_EQUAL = 2;
    const COMPARE_GREATER_THEN = 3;
    const COMPARE_GREATER_THEN_OR_EQUAL = 4;
    const COMPARE_LESS_THEN = 5;
    const COMPARE_LESS_THEN_OR_EQUAL = 6;
    
    private $dependencies;
    private $parent;

    function PackageUpdaterDependency($parent, $dependencies)
    {
        $this->parent = $parent;
        $this->dependencies = $dependencies;
    }

    function get_dependencies()
    {
        return $this->dependencies;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function add_message($message, $type = PackageUpdater :: TYPE_NORMAL)
    {
        $this->get_parent()->add_message($message, $type);
    }

    function update_failed($error_message)
    {
        $this->get_parent()->update_failed($error_message);
    }

    function update_successful($type)
    {
        $this->get_parent()->update_succesful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
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

    function verify()
    {
        $dependencies = $this->get_dependencies();
        
        foreach ($dependencies as $dependency)
        {
            if (! $this->check($dependency))
            {
                switch ($dependency['severity'])
                {
                    case self :: FAILURE_CRITICAL :
                        return false;
                        break;
                    case self :: FAILURE_HIGH :
                        return false;
                        break;
                    case self :: FAILURE_MEDIUM :
                        return true;
                        break;
                    case self :: FAILURE_LOW :
                        return true;
                        break;
                    case self :: FAILURE_VERY_LOW :
                        return true;
                        break;
                    default :
                        return false;
                        break;
                }
            }
            else
            {
                return true;
            }
        }
        
        return true;
    }

    abstract function check($dependency);

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of package installer type.
     */
    static function factory($parent, $type, $dependencies)
    {
        $class = 'PackageUpdater' . Utilities :: underscores_to_camelcase($type) . 'Dependency';
        require_once dirname(__FILE__) . '/dependency/' . $type . '.class.php';
        return new $class($parent, $dependencies);
    }
}
?>
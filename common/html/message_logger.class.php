<?php
class MessageLogger
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';
    
    private static $instances;
    
    private $messages;

    static function get_instance_by_name($instance_name)
    {
        if (! self :: $instances[$instance_name])
        {
            self :: $instances[$instance_name] = new MessageLogger();
        }
        
        return self :: $instances[$instance_name];
    }

    static function get_instance(object $object)
    {
        $class_name = Utilities :: camelcase_to_underscores(get_class($object));
        return self :: get_instance_by_name($class_name);
    }
    
    static function get_instances()
    {
        return self :: $instances;
    }

    function MessageLogger()
    {
        $this->messages = array();
    }

    function add_message($message, $type = self :: TYPE_NORMAL)
    {
        switch ($type)
        {
            case self :: TYPE_NORMAL :
                $this->messages[] = $message;
                break;
            case self :: TYPE_CONFIRM :
                $this->messages[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_WARNING :
                $this->messages[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_ERROR :
                $this->messages[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->messages[] = $message;
                break;
        }
    }

    function set_messages($messages)
    {
        $this->messages = $messages;
    }

    function get_messages()
    {
        return $this->messages;
    }

    function truncate()
    {
        $this->set_messages(array());
    }

    function render()
    {
        $message = implode('<br />' . "\n", $this->get_messages());
        $this->truncate();
        return $message;
    }
    
    function render_for_cli()
    {
    	$message = strip_tags(implode("\n", $this->get_messages()));
        $this->truncate();
        return $message;
    }
}
?>
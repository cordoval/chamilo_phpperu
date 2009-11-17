<?php
class DebugUtilities
{
    public static function show($object, $title = null, $backtrace_index = 0)
    {
        echo '<div class="debug">';
        
        $calledFrom = debug_backtrace();
        echo '<strong>' . $calledFrom[$backtrace_index]['file'] . '</strong>';
        echo ' (line <strong>' . $calledFrom[$backtrace_index]['line'] . '</strong>)';
        
        if (isset($title))
        {
            echo '<h3>' . $title . '</h3>';
        }
        
        echo ('<pre>');
        if (is_array($object))
        {
            print_r($object);
        }
        elseif (is_a($object, 'DOMDocument'))
        {
            $object->formatOutput = true;
            $xml_string = $object->saveXML();
            echo htmlentities($xml_string);
        }
        elseif (is_a($object, 'DOMNodeList') || is_a($object, 'DOMElement'))
        {
            $dom = new DOMDocument();
            $debugElement = $dom->createElement('debug');
            $dom->appendChild($debugElement);
            
            if (is_a($object, 'DOMNodeList'))
            {
                foreach ($object as $node)
                {
                    $node = $dom->importNode($node, true);
                    $debugElement->appendChild($node);
                }
            }
            elseif (is_a($object, 'DOMElement'))
            {
                $node = $dom->importNode($object, true);
                $debugElement->appendChild($node);
            }
            
            $dom->formatOutput = true;
            $xml_string = $dom->saveXML();
            echo htmlentities($xml_string);
        }
        elseif (is_object($object))
        {
            echo print_r($object);
        }
        else
        {
            echo $object;
        }
        
        echo ('</pre>');
        echo '</div>';
    }
}

/*******************************************************************/
/***** GLOBAL FUNCTIONS ********************************************/
/*******************************************************************/

function debug($object, $title = null)
{
    Debug :: show($object, $title, 1);
}
?>
<?php
namespace common\libraries;

/**
 * Class used to process simple text templates. Mostly used to replace {$variables} in strings.
 *
 * @TODO: remove that when we move to a templating system
 * @TODO: could be more efficient to do an include or eval
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class SimpleTemplate {

    private static $instance = null;

    /**
     *
     * @return SimpleTemplate
     */
    public static function get_instance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Replaces $name=>$value pairs comming from $vars from $template.
     *
     * @param string|array $template
     * @param array $vars
     * @return string
     */
    public static function ex($template, $vars){
        $instance = self::get_instance();
        return $instance->process_one($template, $vars);
    }

    /**
     * Process $template once for each entries in $vars. Join result with $glue.
     *
     * @param string|array $template
     * @param array $vars
     * @param string $glue
     * @return string
     */
    public static function all($template, $vars, $glue = null){
        $instance = self::get_instance();
        return $instance->process_all($template, $vars, $glue);
    }
    

    private $template_callback_context = array();
    private $glue = StringUtilities::NEW_LINE;

    public function  __construct($glue = StringUtilities::NEW_LINE) {
        $this->glue = $glue;
    }

    public function process_one($template, $vars){
        if(is_array($template)){
            $template = implode($this->glue, $template);
        }

        $pattern = '/\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/';
        $this->template_callback_context = $vars;
        $result = preg_replace_callback($pattern, array($this, 'process_template_callback'), $template);
        $items = preg_split($pattern, $template);
        $this->template_callback_context = array();
        return $result;
    }

    public function process_all($template, $items, $glue = null){
        $result = array();
        foreach($items as $item){
            $result[] = $this->process_one($template, $item);
        }
        $glue = is_null($glue) ? $this->glue : $glue;
        return implode($glue, $result);
    }

    private function process_template_callback($matches){
        $vars = $this->template_callback_context;
        $name = trim($matches[0], '{$}');
        $result =  isset($vars[$name]) ? $vars[$name] : '';
        if(is_array($result)){
            $result = implode($this->glue, $result);
        }
        return $result;
    }
}
?>

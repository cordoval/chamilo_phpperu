<?php
/**
 * $Id: reporting_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */

//require_once Path :: get_plugin_path() . '/pear/Pager/Pager.php';

abstract class ReportingFormatter
{
	protected $block;
    
    function ReportingFormatter($block)
    {
    	$this->set_block($block);
    }
    
    /**
     * Generates the html representing the chosen display mode
     * @return html
     */
    abstract function to_html();

    public static function factory($reporting_block)
    {
        $type = $reporting_block->get_displaymode();
        if (strpos($type, 'Chart:') !== false)
        {
            $type = 'Chart';
        }
        
        require_once dirname(__FILE__) . '/formatters/reporting_' . strtolower($type) . '_formatter.class.php';
        $class = 'Reporting' . $type . 'Formatter';
        return new $class($reporting_block);
    } 
    
    public function get_block()
    {
    	return $this->block;
    }
    
    public function set_block($block)
    {
    	$this->block = $block;
    }
    
    /*protected function get_pager_links($pager)
    {
        return '<div class="page" style="text-align: center; margin: 1em 0;">' . $pager_links .= $pager->links . '</div>';
    }

    protected function create_pager($params)
    {
        return Pager :: factory($params);
    }*/

} 
?>
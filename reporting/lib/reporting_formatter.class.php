<?php
/**
 * $Id: reporting_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */

require_once Path :: get_plugin_path() . '/pear/Pager/Pager.php';

abstract class ReportingFormatter
{
	protected $block;
	const DISPLAY_TEXT = 1;
	const DISPLAY_TABLE = 2;
	const DISPLAY_CHART = 3;
	const DISPLAY_HTML = 4;
    
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
        $display_mode = $reporting_block->get_displaymode();
        $display_mode = explode('_', $display_mode);
        $type = self::get_type_name($display_mode[0]);
        require_once dirname(__FILE__) . '/formatters/reporting_' . strtolower($type) . '_formatter.class.php';
        $class = 'Reporting' . Utilities::underscores_to_camelcase($type) . 'Formatter';

        return new $class($reporting_block);
    } 
    
    function get_type_name($value)
    {
    	switch($value)
    	{
    		case self::DISPLAY_TEXT : return 'text'; break;
    		case self::DISPLAY_HTML: return 'html'; break;
    		case self::DISPLAY_TABLE : return 'table'; break;
    		case self::DISPLAY_CHART : return 'chart'; break;
    		default : return 'text';
    	}
    }
    
    public function get_block()
    {
    	return $this->block;
    }
    
    public function set_block($block)
    {
    	$this->block = $block;
    }
    
    protected function get_pager_links($pager)
    {
        return '<div class="page" style="text-align: center; margin: 1em 0;">' . $pager_links .= $pager->links . '</div>';
    }

    protected function create_pager($params)
    {
        return Pager :: factory($params);
    }

} 
?>
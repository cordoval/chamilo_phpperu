<?php
/**
 * $Id: reporting_blocks.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */
class ReportingBlocks
{

    /**
     * Creates a reporting block in the database
     * @param array $array
     * @return ReportingBlock
     */
    public static function create_reporting_block_registration($array)
    {
        $reporting_block = new ReportingBlockRegistration();
        $reporting_block->set_default_properties($array);
        if (! $reporting_block->create())
        {
            return false;
        }
        return $reporting_block;
    }
}
?>
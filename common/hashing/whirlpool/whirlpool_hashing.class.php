<?php
/**
 * $Id: whirlpool_hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.hashing.whirlpool
 */
/**
 * Class that defines whirlpool hashing
 * @author Samumon
 *
 */
class WhirlpoolHashing extends Hashing
{

    function create_hash($value)
    {
        return hash('whirlpool', $value);
    }

    function create_file_hash($file)
    {
        return hash_file('whirlpool', $file);
    }

}
?>
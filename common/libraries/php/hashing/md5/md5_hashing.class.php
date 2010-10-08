<?php
/**
 * $Id: md5_hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.hashing.md5
 */
/**
 * Class that defines md5 hashing
 * @author vanpouckesven
 *
 */
class Md5Hashing extends Hashing
{

    function create_hash($value)
    {
        return md5($value);
    }

    function create_file_hash($file)
    {
        return md5_file($file);
    }

}
?>
<?php
namespace common\libraries;
/**
 * $Id: sha512_hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.hashing.sha512
 */
/**
 * Class that defines sha512 hashing
 * @author vanpouckesven
 *
 */
class Sha512Hashing extends Hashing
{

    function create_hash($value)
    {
        return hash('sha512', $value);
    }

    function create_file_hash($file)
    {
        return hash_file('sha512', $file);
    }

}
?>
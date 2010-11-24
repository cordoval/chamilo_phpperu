<?php
namespace repository;

use common\libraries\StaticTableColumn;

/**
 * Column where the cells render the available actions
 *
 * @author Pieterjan Broekaert
 */
class ActionColumn extends StaticTableColumn
{

    function __construct()
    {
        parent :: __construct('');

    }
}

?>

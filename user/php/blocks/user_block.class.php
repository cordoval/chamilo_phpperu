<?php

namespace user;

use common\libraries\Block;

/**
 * @author Hans De bisschop
 */
class UserBlock extends Block
{

    function is_visible()
    {
        return true; //i.e.display on homepage when anonymous
    }

}

?>
<?php
namespace common\libraries;
require_once Path :: get_library_path() . 'application_component.class.php';

class WebApplicationComponent extends ApplicationComponent
{

    /**
     * The WebApplicationComponent constructor
     * @see ApplicationComponent :: __construct()
     */
    function __construct($manager)
    {
        parent :: __construct($manager);
    }
}
?>
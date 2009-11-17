<?php
/**
 * $Id: geolocation_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class GeolocationToolComponent extends ToolComponent
{

    static function factory($component_name, $tool)
    {
        return parent :: factory('Geolocation', $component_name, $tool);
    }
}
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of complex_builderclass
 *
 * @author jevdheyd
 */
class GlossaryToolComplexBuilderComponent extends GlossaryTool {

    function run()
    {
        $builder = ToolComponent :: factory(ToolComponent :: BUILD_COMPLEX_CONTENT_OBJECT_COMPONENT, $this);
        $builder->run();
    }
}
?>

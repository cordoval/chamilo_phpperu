<?php
/**
 * Description of deleterclass
 *
 * @author jevdheyd
 */
class GlossaryDisplayDeleterComponent extends GlossaryDisplay
{
    function run()
    {
        $deleter =  ComplexDisplayComponent::factory(ComplexDisplayComponent::DELETER_COMPONENT, $this);
        $deleter->run();
    }
}
?>

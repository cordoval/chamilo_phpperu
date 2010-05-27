<?php
/**
 * Description of createclass
 *
 * @author jevdheyd
 */
class GlossaryDisplayCreatorComponent extends GlossaryDisplay
{
    function run()
    {
        $creator =  ComplexDisplayComponent::factory(ComplexDisplayComponent::CREATOR_COMPONENT, $this);
        $creator->run();
    }
}
?>

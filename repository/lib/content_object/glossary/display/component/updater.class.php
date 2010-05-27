<?php
/**
 * Description of updateclass
 *
 * @author jevdheyd
 */
class GlossaryDisplayUpdaterComponent extends GlossaryDisplay
{
    function run()
    {
        $updater =  ComplexDisplayComponent::factory(ComplexDisplayComponent::UPDATER_COMPONENT, $this);
        $updater->run();
    }
}
?>

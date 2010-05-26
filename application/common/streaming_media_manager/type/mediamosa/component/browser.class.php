<?php

/**
 * Description of browserclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerBrowserComponent extends MediaMosaStreamingMediaManager{

    function run()
    {
        $browser = StreamingMediaComponent::factory(StreamingMediaComponent::BROWSER_COMPONENT, $this);
		
        $browser->run();
    }

}
?>

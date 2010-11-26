<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager\VideoConferencingComponent;

class BbbVideoConferencingManagerCreatorComponent extends BbbVideoConferencingManager
{

    function run()
    {
        VideoConferencingComponent :: launch($this);
    }
}
?>
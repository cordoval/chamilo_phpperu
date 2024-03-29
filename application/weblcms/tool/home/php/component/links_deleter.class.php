<?php
namespace application\weblcms\tool\home;

use application\weblcms\WeblcmsDataManager;
use application\weblcms\Tool;
use common\libraries\Request;
use common\libraries\Translation;

class HomeToolLinksDeleterComponent extends HomeTool
{

    function run()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $pub_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);

        $publication = $wdm->retrieve_content_object_publication($pub_id);
        $publication->set_show_on_homepage(0);
        $succes = $publication->update();

        $message = $succes ? 'PublicationRemovedFromHomepage' : 'PublicationNotRemovedFromHomepage';

        $this->redirect(Translation :: get($message), ! $succes, array(HomeTool :: PARAM_ACTION => HomeTool :: ACTION_BROWSE));
    }
}
?>
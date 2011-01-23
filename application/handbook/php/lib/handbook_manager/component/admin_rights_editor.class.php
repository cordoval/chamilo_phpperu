<?php

namespace application\handbook;

use common\libraries\DelegateComponent;
use common\libraries\Request;
use common\extensions\rights_editor_manager\RightsEditorManager;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use rights\RightsDataManager;
use common\libraries\EqualityCondition;
use rights\Location;

require_once dirname(__FILE__) . '/../../handbook_rights.class.php';

/**
 * Handbook manager component to set the rights for a handbook publication
 */
class HandbookManagerAdminRightsEditorComponent extends HandbookManager implements DelegateComponent {

    /**
     * Runs this component and displays its output.
     */
    function run() {
        $locations[] = HandbookRights::get_handbooks_subtree_root();

        $manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_additional_parameters() {
        return array(self :: PARAM_HANDBOOK_PUBLICATION_ID);
    }

    function get_available_rights() {
        return HandbookRights :: get_available_rights_for_application();
    }

}

?>
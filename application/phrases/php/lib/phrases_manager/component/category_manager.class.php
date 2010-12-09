<?php
namespace application\phrases;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\DelegateComponent;

/**
 * $Id: category_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */
require_once dirname(__FILE__) . '/../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../category_manager/phrases_publication_category_manager.class.php';

/**
 * Component to manage phrases publication categories
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManagerCategoryManagerComponent extends PhrasesManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $category_manager = new PhrasesPublicationCategoryManager($this, $trail);
        $category_manager->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_category_manager');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }
}
?>
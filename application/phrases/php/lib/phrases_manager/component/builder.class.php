<?php
namespace application\phrases;

use common\libraries\DelegateComponent;
use common\libraries\Request;
use repository\ComplexBuilder;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerBuilderComponent extends PhrasesManager implements DelegateComponent
{
    private $content_object;

    function run()
    {
        $publication_id = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($publication_id);

        $this->content_object = $publication->get_publication_object();
        $this->set_parameter(PhrasesManager :: PARAM_PHRASES_PUBLICATION, $publication_id);

        ComplexBuilder :: launch($this->content_object->get_type(), $this);
    }

    function get_root_content_object()
    {
        return $this->content_object;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_builder');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }
}
?>
<?php

namespace application\gradebook;

use common\libraries\WebApplication;
use common\extensions\reporting_viewer\ReportingViewer;
use common\libraries\Request;

require_once WebApplication :: get_application_class_path('gradebook') . 'reporting/templates/publication_evaluations_template.class.php';

class GradebookManagerViewEvaluationsOnPublicationComponent extends GradebookManager
{

    function run()
    {
        $trail = $this->get_general_breadcrumbs();

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('publication_evaluations_template', GradebookManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->set_parameter(GradebookManager :: PARAM_PUBLICATION_TYPE, Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE));
        $rtv->set_parameter(GradebookManager :: PARAM_PUBLICATION_ID, Request :: get(GradebookManager :: PARAM_PUBLICATION_ID));
        $rtv->set_parameter(GradebookManager :: PARAM_PUBLICATION_APP, Request :: get(GradebookManager :: PARAM_PUBLICATION_APP));
        $rtv->show_all_blocks();

        $rtv->run();
    }

}

?>
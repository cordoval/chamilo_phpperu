<?php
namespace application\weblcms;

use common\libraries\Request;
use reporting\ReportingTemplate;

/**
 * $Id: publication_detail_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_publication_detail_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_publication_access_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_publication_user_access_reporting_block.class.php';

class PublicationDetailReportingTemplate extends ReportingTemplate
{

    function __construct($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_publication_detail());
        $this->add_reporting_block($this->get_publication_access());
        $this->add_reporting_block($this->get_publication_user_access());
    }

    function display_context()
    {

    }

    function get_application()
    {
        return WeblcmsManager :: APPLICATION_NAME;
    }

    function get_publication_detail()
    {
        $course_weblcms_block = new WeblcmsPublicationDetailReportingBlock($this);
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $user_id = Request :: get(WeblcmsManager :: PARAM_USERS);
        $tool = Request :: get(WeblcmsManager :: PARAM_TOOL);
        $pid = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);

        if ($course_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_USERS, $user_id);
        }
        if ($tool)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_TOOL, $tool);
        }
        if ($pid)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_PUBLICATION, $pid);
        }
        return $course_weblcms_block;
    }

    function get_publication_access()
    {
        $course_weblcms_block = new WeblcmsPublicationAccessReportingBlock($this);
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $user_id = Request :: get(WeblcmsManager :: PARAM_USERS);
        $tool = Request :: get(WeblcmsManager :: PARAM_TOOL);
        $pid = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);

        if ($course_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_USERS, $user_id);
        }
        if ($tool)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_TOOL, $tool);
        }
        if ($pid)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_PUBLICATION, $pid);
        }
        return $course_weblcms_block;
    }

    function get_publication_user_access()
    {
        $course_weblcms_block = new WeblcmsPublicationUserAccessReportingBlock($this);
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $user_id = Request :: get(WeblcmsManager :: PARAM_USERS);
        $tool = Request :: get(WeblcmsManager :: PARAM_TOOL);
        $pid = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);

        if ($course_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_USERS, $user_id);
        }
        if ($tool)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_TOOL, $tool);
        }
        if ($pid)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_PUBLICATION, $pid);
        }
        return $course_weblcms_block;
    }
}
?>
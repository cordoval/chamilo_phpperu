<?php
namespace common\extensions\validation_manager;
/**
 * $Id: deleter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component
 */

/**
 * Description of deleterclas
 *
 * @author pieter
 */

use application\portfolio\PortfolioManager;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\BreadcrumbTrail;

class ValidationManagerDeleterComponent extends ValidationManagerComponent
{

    function run()
    {
        $html = $this->as_html();

        $this->display_header(BreadcrumbTrail :: get_instance());
        echo $html;
        $this->display_footer();
    }

    function as_html()
    {
        //fouten opvang en id dynamisch ophalen
        //$id = Request :: get(FeedbackPublication :: PROPERTY_ID);


        $pid = Request :: get('pid');
        $user_id = Request :: get('user_id');
        $cid = Request :: get('cid');
        $action = Request :: get('action');

        $id = Request :: get('deleteitem');

        if (! $this->get_user())
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $validation = $this->retrieve_validation($id);

        if ($validation->delete())
        {

            $message = 'ValidationDeleted';
            $succes = true;
        }

        else
        {
            $message = 'ValidationNotDeleted';
            $succes = false;
        }
        $this->redirect(Translation :: get($message), succes ? false : true, array(
                Application :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO,
                'pid' => $pid,
                'cid' => $cid,
                'user_id' => $user_id,
                'action' => $action));

    }
}
?>
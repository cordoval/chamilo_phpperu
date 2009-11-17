<?php
/**
 * $Id: wiki_link_creator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';

class ToolWikiLinkCreatorComponent extends ToolComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses general');
        
        if ($this->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get('pid');
            if (! $pid)
            {
                $this->display_header($trail, true);
                $this->display_error_message(Translation :: get('NoParentSelected'));
                $this->display_footer();
            }
            
            $type = 'wiki_page';
            
            $pub = new ContentObjectRepoViewer($this, $type, true);
            $pub->set_parameter(Tool :: PARAM_ACTION, WikiTool :: ACTION_ADD_LINK);
            $pub->set_parameter('pid', $pid);
            $pub->set_parameter('type', $type);
            
            $object_id = Request :: get('object');
            
            if (! isset($object_id))
            {
                $html[] = '<p><a href="' . $this->get_url(array('type' => $type, 'pid' => $pid)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                $this->display_header($trail, true);
                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
            
            }
        
        }
    }

    private function my_redirect($pid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));
        
        $params = array();
        $params['pid'] = $pid;
        $params['tool_action'] = 'view';
        
        $this->redirect($message, '', $params);
    }

}
?>

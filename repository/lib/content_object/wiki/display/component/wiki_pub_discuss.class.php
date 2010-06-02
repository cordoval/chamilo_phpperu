<?php
/**
 * $Id: wiki_pub_discuss.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the discuss page. Here a user can add feedback to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/component/wiki_parser.class.php';
require_once Path :: get_application_path() . 'lib/wiki/wiki_pub_feedback.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiDisplayWikiPubDiscussComponent extends WikiDisplay
{
    private $action_bar;
    private $wiki_page_id;
    private $complex_id;
    private $feedback_id;
    private $links;
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            $this->display_header(new BreadcrumbTrail());
        	Display :: not_allowed();
        	$this->display_footer();
            return;
        }
        
        $data_manager = RepositoryDataManager :: get_instance();
        
        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        
        $this->complex_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        
        $complexObject = $data_manager->retrieve_complex_content_object_item($this->complex_id);
        if (isset($complexObject))
        {
            $this->wiki_page_id = $complexObject->get_ref();
            $data_manager->retrieve_content_object($this->wiki_page_id);
        }
        $wiki_page = $data_manager->retrieve_content_object($this->wiki_page_id);
        
        $this->display_header(new BreadcrumbTrail());
        
        $this->action_bar = $this->get_toolbar($this, $this->get_root_content_object()->get_id(), $this->get_root_content_object(), $this->complex_id); //$this->get_toolbar();
        echo '<div id="trailbox2" style="padding:0px;">' . $this->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';
        echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . Translation :: get('DiscussThe') . ' ' . $wiki_page->get_title() . ' ' . Translation :: get('Page') . '<hr style="height:1px;color:#4271B5;width:100%;"></div>';
        
        /*
         *  We make use of the existing ContentObjectDisplay class, changing the type to wiki_page
         */
        $display = ContentObjectDisplay :: factory($wiki_page);
        /*
         *  Here we make the call to the wiki_parser.
         *  For more information about the parser, please read the information in the wiki_parser class.
         */
        
        $parser = new WikiDisplayWikiParserComponent($this->get_root_content_object()->get_id(), $display->get_full_html(), $this->complex_id);
        $parser->parse_wiki_text();
        
        $this->set_script();
        echo '<a id="showhide" href="#">[' . Translation :: get('Hide') . ']</a><br /><br />';
        echo '<div id="content" style="line-height: 110%;">' . $parser->get_wiki_text() . '</div><br />';
        
        /*
         *  We make use of the existing condition framework to show the data we want.
         *  If the publication id , and the compled object id are equal to the ones passed the feedback will be shown.
         */
        
        if (isset($this->complex_id) && $this->get_root_content_object()->get_id() != null)
        {
            $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, Request :: get('pid'));
            $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_CLOI_ID, $this->complex_id);
            $condition = new AndCondition($conditions);
            $feedbacks = $data_manager->retrieve_content_object_pub_feedback($condition);
            
            while ($feedback = $feedbacks->next_result())
            {
                if ($i == 0)
                {
                    echo '<div style="font-size:18px;">' . Translation :: get('Feedback') . '</div><hr>';
                    echo $this->show_add_feedback() . '<br /><br />';
                }
                $this->feedback_id = $feedback->get_feedback_id();
                /*
                 *  We retrieve the learning object, because that one contains the information we want to show.
                 *  We then display it using the ContentObjectDisplay and setting the type to feedback
                 */
                $feedback_display = $data_manager->retrieve_content_object($this->feedback_id);
                
                echo $this->show_feedback($feedback_display);
                $i ++;
            
            }
        }
        
        echo '</div>';
    
        $this->display_footer();
    }

    function build_feedback_actions()
    {
        $actions[] = array('href' => $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DELETE_FEEDBACK, ' WikiPubFeedback :: PROPERTY_FEEDBACK_ID' => $this->feedback_id, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id, 'pid' => Request :: get('pid'))), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        
        $actions[] = array('href' => $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_EDIT_FEEDBACK, ' WikiPubFeedback :: PROPERTY_FEEDBACK_ID' => $this->feedback_id, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id, 'pid' => Request :: get('pid'))), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        return Utilities :: build_toolbar($actions);
    
    }

    function show_add_feedback()
    {
        $actions[] = array('href' => $this->get_url(array(WikiTool :: PARAM_ACTION => Tool :: ACTION_FEEDBACK_CLOI, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)), 'label' => Translation :: get('AddFeedback'), 'img' => Theme :: get_common_image_path() . 'action_add.png', 'confirm' => false);
        
        return Utilities :: build_toolbar($actions);
    
    }

    private function show_feedback($object)
    {
        $creationDate = $object->get_creation_date();
        
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
        $html[] = '<div class="title">' . htmlentities($object->get_title()) . ' | ' . htmlentities(date("F j, Y, H:i:s", $creationDate)) . '</div>';
        $html[] = self :: TITLE_MARKER;
        $html[] = 'fu';
        $html[] = $object->get_description();
        $html[] = self :: DESCRIPTION_MARKER;
        $html[] = '<div style="float:right">' . $this->build_feedback_actions() . '</div>';
        $html[] = '<br />';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    private function set_script()
    {
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/showhide_content.js');
        ;
    }

}

?>
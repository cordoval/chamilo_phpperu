<?php

/*
 * This is a standalone wiki parser component, used to parse links to other wiki pages, much in the same way as on Wikipedia.
 * A normal wiki page link looks like [[*title of wiki page*]]
 * A | character can also be used to give the link a title different from the page title. E.g: [[*title of wiki page*|*title of URL*]]
 * The pid is the publication ID of the wiki, and the course id is the id of the course wherein the parent wiki resides.
 * For the moment it's only possible to link to other wiki pages in the same wiki.
 * A normal content link looks like ==*title of header*==
 * The more == are used, the lower in the index it will be placed
 * fe :
 *      ==Root==
 *      ==subRoot==
 * would become :   1. Root
 *                  1.1 subRoot
 * The contentbox can be shown and hidden by using actionscript
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

class WikiParser
{
    private $wiki_id;
    private $selected_cloi;
    private $wikiText;
    private $parent;

    function WikiParser($parent, $wikiId, $wikiText, $cId)
    {
        $this->parent = $parent;
    	$this->wiki_id = $wikiId;
        $this->wikiText = $wikiText;
        $this->selected_cloi = $cId;
    }

    function set_parent($parent)
    {
    	$this->parent = $parent;
    }
    
    function get_parent()
    {
    	return $this->parent;
    }
    
    function get_url($parameters = array())
    {
    	return $this->parent->get_url($parameters);
    }
    
    function set_wiki_id($value)
    {
        $this->wiki_id = $value;
    }

    function get_wiki_id()
    {
        return $this->wiki_id;
    }

    function get_wiki_text()
    {
        return $this->wikiText;
    }

    function set_wiki_text($value)
    {
        $this->wikiText = $value;
    }

    public function parse_wiki_text()
    {
        $this->handle_internal_links();
        $this->handle_doubt_tags();
        return $this->create_wiki_contentsbox();

    }

    private function handle_internal_links()
    {
        $linkCount = substr_count($this->wikiText, '[[');

        for($i = 0; $i < $linkCount; $i ++)
        {
            $first = stripos($this->wikiText, '[[');
            $last = stripos($this->wikiText, ']]');
            $title = substr($this->wikiText, $first + 2, $last - $first - 2);
            $pipe = strpos($title, '|');
            if ($pipe === false)
                $this->wikiText = substr_replace($this->wikiText, $this->get_wiki_page_url($title), $first, $last - $first + 2);
            else
            {
                $title = explode('|', $title);
                $this->wikiText = substr_replace($this->wikiText, $this->get_wiki_page_url($title[0], $title[1]), $first, $last - $first + 2);
            }
        }
    }

    private function get_wiki_page_url(&$title, $viewTitle = null)
    {
        $pages = RepositoryDataManager :: get_instance()->retrieve_type_content_objects(WikiPage :: get_type_name(), new EqualityCondition(ContentObject :: PROPERTY_TITLE, $title))->as_array();
        if ($viewTitle != null)
            $title = $viewTitle;
        foreach ($pages as $page)
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition('ref_id', $page->get_id()))->next_result();
            if (! empty($cloi))
                break;
        }
        if (! empty($cloi))
        {
            $url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id, 'selected_cloi' => $cloi->get_id()));
            //$url = (Redirect ::get_url(array('go' => 'courseviewer', strtolower(Course ::CLASS_NAME) => $this->course_id, 'tool' => 'wiki', 'application' => 'weblcms', Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id, 'selected_cloi' => $cloi->get_id())));
            return '<a href="' . $url . '">' . htmlspecialchars($title) . '</a>';
        }
        else
        {
            $url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id, 'title' => $title));
            //$url = (Redirect ::get_url(array('go' => 'courseviewer', strtolower(Course ::CLASS_NAME) => $this->course_id, 'tool' => 'wiki', 'application' => 'weblcms', Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id, 'title' => $title)));
            return '<a class="does_not_exist" href="' . $url . '">' . htmlspecialchars($title) . '</a>';
        }
    }

    private function get_wiki_page_discussion_url()
    {
        //$url = (Redirect ::get_url(array('go' => 'courseviewer', strtolower(Course ::CLASS_NAME) => $this->course_id, 'tool' => 'wiki', 'application' => 'weblcms', Tool :: PARAM_ACTION => Request :: get(Tool :: PARAM_ACTION), WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'selected_cloi' => $this->selected_cloi)));
        return '<a href="' . $url . '">' . Translation :: get('discussionPage') . '</a>';
    }

    private function create_wiki_contentsbox()
    {
        $pattern = '/(==+[[:print:] àèùìòáéúíóäëÿüïöÀÈÙÌÒÁÉÚÍÓÄËŸÜÏÖ]+==+)/u';
        $linkCount = preg_match($pattern, $this->wikiText);
        $list = $this->parse_wiki_headers($this->wikiText);

        if ($linkCount > 0)
        {
            $this->set_script();
            $html = array();

            $html[] = '<div id="top" name="top">';
            $html[] = '<div id="contentbox">' . Translation :: get('Contents');
            $html[] = '<a id="showhide" href="#">[' . Translation :: get(Hide) . ']</a><br /></div>';
            $html[] = '<br />';
            $html[] = '<div id="content">';
            $html[] = $this->fill_content_box($list);
            $html[] = '</div></div>';

            return implode("\n", $html);

        }
    }

    private function parse_wiki_headers()
    {
        $list = array();

        $pattern = '/(==+[[:print:] àèùìòáéúíóäëÿüïöÀÈÙÌÒÁÉÚÍÓÄËŸÜÏÖ]+==+)/u';
        preg_match_all($pattern, $this->wikiText, $matches, PREG_PATTERN_ORDER);

        foreach ($matches[1] as $value)
        {
            $old_link = $value;
            $head = substr_count($value, '=') / 2 - 1;
            $heads[$head] ++;
            $value = str_replace('<p>', '', $value);
            $value = str_replace('=', '', $value);
            $value = str_replace('</p>', '', $value);
            $this->reset_heads($heads, $head + 1);
            switch ($head)
            {
                case 1 :
                    {
                        $index[$value] = $heads[1];
                        break;
                    }
                case 2 :
                    {
                        $index[$value] = ' ' . $heads[1] . '.' . $heads[2];
                        break;
                    }
                case 3 :
                    {
                        $index[$value] = '  ' . $heads[1] . '.' . $heads[2] . '.' . $heads[3];
                        break;
                    }
                case 4 :
                    {
                        $index[$value] = '   ' . $heads[1] . '.' . $heads[2] . '.' . $heads[3] . '.' . $heads[4];
                        break;
                    }
                case 5 :
                    {
                        $index[$value] = '    ' . $heads[1] . '.' . $heads[2] . '.' . $heads[3] . '.' . $heads[4] . '.' . $heads[5];
                        break;
                    }
                case 6 :
                    {
                        $index[$value] = '     ' . $heads[1] . '.' . $heads[2] . '.' . $heads[3] . '.' . $heads[4] . '.' . $heads[5] . '.' . $heads[6];
                        break;
                    }
            }
            $top = '<div style="float:right">
                    <a href="#top">
                    <img alt="" src="' . Theme :: get_common_image_path() . '/action_ajax_add.png"/>
                    </a>
                    </div>';
            $value = $top . '<p class="head' . $head . '" id ="' . str_replace(' ', '', $value) . '">' . $value . '</p><hr>';
            $this->wikiText = str_replace($old_link, $value, $this->wikiText);
        }
        return $index;
    }

    private function handle_doubt_tags()
    {
        $url = Theme :: get_common_image_path() . 'status_doubt.png';
        $doubts = substr_count($this->wikiText, '{{' . Translation :: get('Disputed') . '}}');

        for($i = 0; $i < $doubts; $i ++)
        {
            $first = stripos($this->wikiText, '{{');
            $last = stripos($this->wikiText, '}}');

            $doubtBox = '<div name="doubt" style="padding:5px;border:1px solid #4271B5;background-color:#faf7f7; margin-left: 10%; margin-right: 10%;">' . '<div style="text-align:center;font-weight:bold;font-size:15px">' . Translation :: get('ThereIsDoubtAboutTheFactualAccuracyOfThisPart') . '</div>' . '<div style="align:left;margin-left:5%;"><img src="' . $url . '" /></div>' . '<div style="text-align:center;font-family:Arial;">' . Translation :: get('ConsultThe') . ' ' . $this->get_wiki_page_discussion_url() . ' ' . Translation :: get('ForMoreInformationAndModifyTheArticleIfDesirable') . '</div>' . '</div>';

            $this->wikiText = str_replace('{{' . Translation :: get('Disputed') . '}}', $doubtBox, $this->wikiText);
        }
    }

    private function reset_heads(&$heads, $start)
    {
        for($i = $start; $i < 7; $i ++)
        {
            $heads[$i] = 0;
        }
    }

    private function fill_content_box($list)
    {
        foreach ($list as $key => $value)
        {
            $head_number = substr_count($value, '.') + 1;
            $html .= '<a style="padding-left: ' . $head_number . '0px" href ="#' . str_replace(' ', '', $key) . '">' . $value . ' ' . $key . '</a><br />';
        }

        return $html;

    }

    private function set_script()
    {
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/showhide_content.js');
    }

    public function handle_toolbox_links($links)
    {
        $this->set_wiki_text($links);
        $this->handle_internal_links();
        $this->wikiText = explode(';', $this->wikiText);
        return $this->get_wiki_text();
    }

    public function get_title_from_wiki_tag($tag, $viewTitle = false)
    {
        $first = stripos($tag, '[[');
        $last = stripos($tag, ']]');
        $title = substr($tag, $first + 2, $last - $first - 2);

        $pipe = strpos($title, '|');
        if ($pipe === false)
            return $title;
        else
        {
            $title = explode('|', $title);
            if ($viewTitle)
                return $title[1];
            else
                return $title[0];
        }
    }

    public function get_pid_from_url($link)
    {
        $pattern = '/(pid=[0-9]*)/';

        preg_match_all($pattern, $link, $matches, PREG_PATTERN_ORDER);

        foreach ($matches as &$match)
        {
            $match = str_replace('pid=', '', $match);
        }

        return $matches[0][0];
    }

    public function get_cid_from_url($link)
    {
        $pattern = '/(selected_cloi=[0-9]*)/';

        preg_match_all($pattern, $link, $matches, PREG_PATTERN_ORDER);

        foreach ($matches as &$match)
        {
            $match = str_replace('selected_cloi=', '', $match);
        }

        return $matches[0][0];
    }

    public function get_title_from_url($link)
    {
        $items = explode('.', $link);
        return $items[1];
    }
}

?>
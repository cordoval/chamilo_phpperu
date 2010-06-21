<?php
/**
 * $Id: wiki_history.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */

require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_parser.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiDisplayWikiHistoryComponent extends WikiDisplay
{
    private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $complex_id;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $data_manager = RepositoryDataManager :: get_instance();
        $repository_manager = new RepositoryManager();

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
        }

        $wiki_page = $data_manager->retrieve_content_object($this->wiki_page_id);

        /*
         *  We make use of the existing ContentObjectDisplay class, changing the type to wiki_page
         */
        $display = ContentObjectDisplay :: factory($wiki_page);

        /*
         *  We make a new array called version_data, this will hold every version for a wiki_page.
         *  A new version is created after an edit to the page is made, and the user chose to create a new version.
         */
        $version_data = array();
        $publication_attr = array();
        $versions = $wiki_page->get_content_object_versions();

        $this->display_header();

        $this->action_bar = $this->get_toolbar($this, Request :: get('pid'), $this->get_root_content_object(), $this->complex_id);
        //echo '<div id="trailbox2" style="padding:0px;">' . $this->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';
        echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . Translation :: get('HistoryForThe') . ' ' . $wiki_page->get_title() . ' ' . Translation :: get('Page') . '</div><hr style="height:1px;color:#4271B5;width:100%;">';

        /*
         * All versions for a wiki_page will be looped and the publications attributes are stored in the $publication_attr array
         */
        foreach ($versions as $version)
        {
            // If this learning object is published somewhere in an application, these locations are listed here.
            $publications = RepositoryDataManager :: get_content_object_publication_attributes($this->get_user(), $version->get_id());
            $publication_attr = array_merge($publication_attr, $publications);
        }

        /*
         *  If the page has more then version
         *  Every version will be looped and it's information stored in the version_entry array.
         */
        if (count($versions) >= 2)
        {
            //Utilities :: order_content_objects_by_id_desc($versions);
            foreach ($versions as $version)
            {
                $version_entry = array();
                $version_entry['id'] = $version->get_id();
                if (strlen($version->get_title()) > 20)
                {
                    $version_entry['title'] = substr($version->get_title(), 0, 20) . '...';
                }
                else
                {
                    $version_entry['title'] = $version->get_title();
                }
                $version_entry['date'] = date('d M y, H:i', $version->get_creation_date());
                $version_entry['comment'] = $version->get_comment();
                //$version_entry['viewing_link'] = $repository_manager->get_content_object_viewing_url($version);
                $version_entry['viewing_link'] = "http://localhost/index_repository_manager.php?go=view&category={$version->get_parent_id()}&object=" . $version->get_id();
                //$delete_url = $repository_manager->get_content_object_deletion_url($version, 'version');
                //$delete_url = "http://localhost/index_repository_manager.php?go=delete&category={$version->get_parent_id()}&object={$version->get_id()}&delete_version=1";
                if (isset($delete_url))
                {
                    $version_entry['delete_link'] = $delete_url;
                }

                //$revert_url = $repository_manager->get_content_object_revert_url($version, 'version');
                if (isset($revert_url))
                {
                    $version_entry['revert_link'] = $revert_url;
                }

                $version_data[] = $display->get_version_as_html($version_entry);
            }

            /*
             *  Here the compare form is made. It will redirect to the history page passing the right parameters to compare.
             *  You can select 2 versions to compare.
             *  The first selected version ('object') will be compared with the second selected version ('compare') and it's differences shown using the ContentObjectDifferenceDisplay
             */
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_COMPARE, $wiki_page, 'compare', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Request :: get('tool') == 'learning_path' ? 'view_clo' : 'view', 'display_action' => 'history', 'pid' => $this->get_root_content_object()->get_id(), ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id)), array('version_data' => $version_data));

            if ($form->validate())
            {
                $params = $form->compare_content_object();
                $object = $data_manager->retrieve_content_object($params['compare']);
                $diff = $object->get_difference($params['object']);
                $diff_display = ContentObjectDifferenceDisplay :: factory($diff);
                /*
                  *  A block hider is added to hide , and show the legend for the ContentObjectDifferenceDisplay
                  */

                echo $diff_display->get_diff_as_html();
                echo Utilities :: add_block_hider();
                echo Utilities :: build_block_hider('compare_legend');
                echo $diff_display->get_legend();
                echo Utilities :: build_block_hider();
                echo '<br />' . $display->get_version_quota_as_html($version_data);

            }

            $form->display();
        }
        else
        {
            echo Translation :: get('NoModificationsMadeToThisPage');
        }

        echo '</div>';

        $this->display_footer();
    }
}
?>
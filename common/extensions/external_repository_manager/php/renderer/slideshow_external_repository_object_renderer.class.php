<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../external_repository_object_renderer.class.php';

class SlideshowExternalRepositoryObjectRenderer extends ExternalRepositoryObjectRenderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

    function as_html()
    {
        if (! Request :: get(self :: SLIDESHOW_INDEX))
        {
            $slideshow_index = 0;
        }
        else
        {
            $slideshow_index = Request :: get(self :: SLIDESHOW_INDEX);
        }

        $external_repository_object = $this->retrieve_external_repository_objects(null, null, $slideshow_index, 1)->next_result();
        $external_repository_object_count = $this->count_external_repository_objects(null);
        if ($external_repository_object_count == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoExternalRepositoryObjectsAvailable'), true);
            return implode("\n", $html);
        }

        $first = ($slideshow_index == 0);
        $last = ($slideshow_index == $external_repository_object_count - 1);

        $play_toolbar = new Toolbar();
        $play_toolbar->add_items($this->get_external_repository_object_actions($external_repository_object));
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            $play_toolbar->add_item(new ToolbarItem(Translation :: get('Stop'), Theme :: get_common_image_path() . 'action_stop.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX),
                    self :: SLIDESHOW_AUTOPLAY => null)), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $play_toolbar->add_item(new ToolbarItem(Translation :: get('Play'), Theme :: get_common_image_path() . 'action_play.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX),
                    self :: SLIDESHOW_AUTOPLAY => 1)), ToolbarItem :: DISPLAY_ICON));
        }

        $navigation_toolbar = new Toolbar();
        if (! $first)
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First'), Theme :: get_common_image_path() . 'action_first.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => 0)), ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => $slideshow_index - 1)), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First'), Theme :: get_common_image_path() . 'action_first_na.png', null, ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        if (! $last)
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Next'), Theme :: get_common_image_path() . 'action_next.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => $slideshow_index + 1)), ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Last'), Theme :: get_common_image_path() . 'action_last.png', $this->get_url(array(
                    self :: SLIDESHOW_INDEX => $publication_count - 1)), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Next'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Last'), Theme :: get_common_image_path() . 'action_last_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        $table = array();
        $table[] = '<table id="slideshow" class="data_table">';
        $table[] = '<thead>';
        $table[] = '<tr>';
        $table[] = '<th class="actions" style="width: 25%; text-align: left;">';
        $table[] = $play_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($external_repository_object->get_title()) . ' - ' . ($slideshow_index + 1) . '/' . $external_repository_object_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ExternalRepositoryObjectDisplay :: factory($external_repository_object)->get_preview();
        $table[] = '</td></tr>';

        $table[] = '</tbody>';
        $table[] = '</table>';

        $table[] = ExternalRepositoryObjectDisplay :: factory($external_repository_object)->get_properties_table();

        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            if (! $last)
            {
                $autoplay_url = $this->get_url(array(
                        self :: SLIDESHOW_AUTOPLAY => 1,
                        self :: SLIDESHOW_INDEX => $slideshow_index + 1));
            }
            else
            {
                $autoplay_url = $this->get_url(array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => 0));
            }

            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplay_url . '" />';
        }

        $html[] = implode("\n", $table);
        return implode("\n", $html);
    }
}
?>
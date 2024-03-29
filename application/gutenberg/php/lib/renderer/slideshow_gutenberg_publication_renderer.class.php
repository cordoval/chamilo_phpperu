<?php
namespace application\gutenberg;

use common\libraries\Utilities;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;

use repository\ContentObjectDisplay;

class SlideshowGutenbergPublicationRenderer extends GutenbergPublicationRenderer
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

        $publication = $this->retrieve_gutenberg_publications($this->get_condition(), null, $slideshow_index, 1)->next_result();
        $publication_count = $this->count_gutenberg_publications($this->get_condition());
        if ($publication_count == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoGutenbergPublicationsAvailable'), true);
            return implode("\n", $html);
        }

        $content_object = $publication->get_publication_object();

        $first = ($slideshow_index == 0);
        $last = ($slideshow_index == $publication_count - 1);

        $parameters = $this->get_parameters();

        $play_toolbar = new Toolbar();
        $play_toolbar->add_items($this->get_gutenberg_publication_actions($publication));
        if ($publication_count > 1)
        {
            if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
            {
                $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
                $parameters[self :: SLIDESHOW_AUTOPLAY] = null;

                $play_toolbar->add_item(new ToolbarItem(Translation :: get('Stop'), Theme :: get_common_image_path() . 'action_stop.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
                $parameters[self :: SLIDESHOW_AUTOPLAY] = 1;

                $play_toolbar->add_item(new ToolbarItem(Translation :: get('Play'), Theme :: get_common_image_path() . 'action_play.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));
            }
        }

        $parameters = $this->get_parameters();

        $navigation_toolbar = new Toolbar();
        if ($publication_count > 1)
        {
            if (! $first)
            {
                $parameters[self :: SLIDESHOW_INDEX] = 0;
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_first.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));

                $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index - 1;
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_prev.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_first_na.png', null, ToolbarItem :: DISPLAY_ICON));
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_prev_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

            if (! $last)
            {
                $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index + 1;
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_next.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));

                $parameters[self :: SLIDESHOW_INDEX] = $publication_count - 1;
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_last.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
                $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_last_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }
        }

        $table = array();
        $table[] = '<table id="slideshow" class="data_table">';
        $table[] = '<thead>';
        $table[] = '<tr>';
        $table[] = '<th class="actions" style="width: 25%; text-align: left;">';
        $table[] = $play_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($content_object->get_title()) . ' - ' . ($slideshow_index + 1) . '/' . $publication_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ContentObjectDisplay :: factory($content_object)->get_preview();
        $table[] = '</td></tr>';

        $table[] = '</tbody>';
        $table[] = '</table>';

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
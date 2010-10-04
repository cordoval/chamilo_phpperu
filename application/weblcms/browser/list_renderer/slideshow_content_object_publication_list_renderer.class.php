<?php
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';

class SlideshowContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
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

        $publications = $this->get_publications($slideshow_index, 1);
        $publication = $publications[0];
        $publication_count = $this->get_publication_count();
        if ($publication_count == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoPublicationsAvailable'), true);
            return implode("\n", $html);
        }

        $first = ($slideshow_index == 0);
        $last = ($slideshow_index == $publication_count - 1);

        $content_object = $publication->get_content_object();
        $view_url = $content_object->get_url();
//        $download_url = $this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_DOWNLOAD, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));

//        $resize = Session :: retrieve('slideshow_resize');
//        if ($resize)
//        {
//            list($width, $height) = explode("|", $resize);
//            list($original_width, $original_height, $type, $attr) = getimagesize($document->get_full_path());
//
//            $aspect = $original_height / $original_width;
//            $width = round($height / $aspect);
//
//            $additional_styles = ' width: ' . $width . 'px; height: ' . $height . 'px;';
//        }
//        else
//        {
            $additional_styles = '';
//        }

        $play_toolbar = $this->get_publication_actions($publication, false);
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            $play_toolbar->add_item(new ToolbarItem(Translation :: get('Stop'), Theme :: get_common_image_path() . 'action_stop.png', $this->get_url(array(self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX), self :: SLIDESHOW_AUTOPLAY => null)), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $play_toolbar->add_item(new ToolbarItem(Translation :: get('Play'), Theme :: get_common_image_path() . 'action_play.png', $this->get_url(array(self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX), self :: SLIDESHOW_AUTOPLAY => 1)), ToolbarItem :: DISPLAY_ICON));
        }

        $navigation_toolbar = new Toolbar();
        if (! $first)
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First'), Theme :: get_common_image_path() . 'action_first.png', $this->get_url(array(self :: SLIDESHOW_INDEX => 0)), ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev.png', $this->get_url(array(self :: SLIDESHOW_INDEX => $slideshow_index - 1)), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('First'), Theme :: get_common_image_path() . 'action_first_na.png', null, ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        if (! $last)
        {
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Next'), Theme :: get_common_image_path() . 'action_next.png', $this->get_url(array(self :: SLIDESHOW_INDEX => $slideshow_index + 1)), ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(new ToolbarItem(Translation :: get('Last'), Theme :: get_common_image_path() . 'action_last.png', $this->get_url(array(self :: SLIDESHOW_INDEX => $publication_count - 1)), ToolbarItem :: DISPLAY_ICON));
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
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($content_object->get_title()) . ' - ' . ($slideshow_index + 1) . '/' . $publication_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ContentObjectDisplay::factory($content_object)->get_preview();
        //$table[] = '<a href="' . $download_url . '" target="about:blank"><img src="' . $view_url . '" alt="" style="max-width: 800px; border: 1px solid #f0f0f0;' . $additional_styles . '"/></a>';
        $table[] = '</td></tr>';
        $table[] = '<tr><td class="header" colspan="3">' . Translation :: get('Description') . '</td></tr>';
        $table[] = '<tr><td colspan="3">' . $content_object->get_description() . '</td></tr>';
        $table[] = '</tbody>';
        $table[] = '</table>';

        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            if (! $last)
            {
                $autoplay_url = $this->get_url(array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => $slideshow_index + 1));
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
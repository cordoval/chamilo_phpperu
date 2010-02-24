<?php
class CkeditorFileBrowser
{
    function run()
    {
      $repo = new RepoViewer($this, 'announcement');
      echo $repo->as_html();
    }

    function get_parameters()
    {
        return array();
    }

    function get_user_id()
    {
        return Session :: get_user_id();
    }
}
?>
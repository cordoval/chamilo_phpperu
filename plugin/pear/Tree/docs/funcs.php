<?php
//  $Id: funcs.php 137 2009-11-09 13:24:37Z vanpouckesven $

ini_set('error_reporting', E_ALL);

/**
*   this is a helper function, so i dont have to write so many prints :-)
*   @param  array   $para   the result returned by some method, that will be dumped
*   @param  string  $string the explaining string
*/
function dumpHelper($para, $string = '', $addArray = false)
{
    global $tree, $element;

    if ($addArray) {
        eval("\$res=array(".$para.');');
    } else {
        eval("\$res=".$para.';');
    }
    echo '<strong>' . $para . ' </strong><i><u><span style="color: #008000;">' . $string . '</span></u></i><br>';
    // this method dumps to the screen, since print_r or var_dump dont
    // work too good here, because the inner array is recursive
    // well, it looks ugly but one can see what is meant :-)
    $tree->varDump($res);
    echo '<br />';

}

/**
*   dumps the entire structure nicely
*   @param  string  $string the explaining string
*/
function dumpAllNicely($string = '')
{
    global $tree;

    echo '<i><span style="color: #008000; text-decoration: underline;">' . $string . '</span></i><br>';
    $all = $tree->getBranch();   // get the entire structure sorted as the tree is, so we can simply foreach through it and show it
    foreach ($all as $aElement) {
        for ($i = 0; $i < $aElement['level']; ++$i) {
            echo '&nbsp; &nbsp; ';
        }
        echo '<span style="color: red">' . $aElement['name'] . '</span> ===&gt; ';
        $tree->varDump(array($aElement));
    }
    echo '<br />';

}
<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: allowed_tags.inc.php 128 2009-11-09 13:13:20Z vanpouckesven $
/**
 * This page defines all HTML-tages and their attributes that are allowed in
 * Chamilo. 2 arrays are defined, one contains the allowed HTML for students and
 * the other the allowed HTML for teachers.
 *
 * Modifying this page:
 * - for each allowed tag there should be a line like
 *    $allowed_tags_XXXX   ['tagname'] = array();
 * - for each of the attributes allowed in the tag, there should be a line like
 *    $allowed_tags_XXXX['tagname']['attributename'] = array();
 * - please keep the content of this file alphabetically structured
 */
//============================================================
// ALLOWED HTML FOR STUDENTS
//============================================================
// ADDRESS
$allowed_tags_student['address'] = array();
// APPLET
$allowed_tags_student['applet'] = array();
$allowed_tags_student['applet']['codebase'] = array();
$allowed_tags_student['applet']['code'] = array();
$allowed_tags_student['applet']['name'] = array();
$allowed_tags_student['applet']['alt'] = array();
// AREA
$allowed_tags_student['area'] = array();
$allowed_tags_student['area']['shape'] = array();
$allowed_tags_student['area']['coords'] = array();
$allowed_tags_student['area']['href'] = array();
$allowed_tags_student['area']['alt'] = array();
// A
$allowed_tags_student['a'] = array();
$allowed_tags_student['a']['href'] = array();
$allowed_tags_student['a']['title'] = array();
$allowed_tags_student['a']['rel'] = array();
$allowed_tags_student['a']['rev'] = array();
$allowed_tags_student['a']['name'] = array();
// ABBR
$allowed_tags_student['abbr'] = array();
$allowed_tags_student['abbr']['title'] = array();
// ACRONYM
$allowed_tags_student['acronym'] = array();
$allowed_tags_student['acronym']['title'] = array();
// B
$allowed_tags_student['b'] = array();
// BASE
$allowed_tags_student['base'] = array();
$allowed_tags_student['base']['href'] = array();
// BASEFONT
$allowed_tags_student['basefont'] = array();
$allowed_tags_student['basefont']['size'] = array();
// BDO
$allowed_tags_student['bdo'] = array();
$allowed_tags_student['bdo']['dir'] = array();
// BIG
$allowed_tags_student['big'] = array();
// BLOCKQUOTE
$allowed_tags_student['blockquote'] = array();
$allowed_tags_student['blockquote']['cite'] = array();
// BODY
$allowed_tags_student_full_page['body'] = array();
$allowed_tags_student_full_page['body']['alink'] = array();
$allowed_tags_student_full_page['body']['background'] = array();
$allowed_tags_student_full_page['body']['bgcolor'] = array();
$allowed_tags_student_full_page['body']['link'] = array();
$allowed_tags_student_full_page['body']['text'] = array();
$allowed_tags_student_full_page['body']['vlink'] = array();
// BR
$allowed_tags_student['br'] = array();
// BUTTON
$allowed_tags_student['button'] = array();
$allowed_tags_student['button']['disabled'] = array();
$allowed_tags_student['button']['name'] = array();
$allowed_tags_student['button']['type'] = array();
$allowed_tags_student['button']['value'] = array();
// CAPTION
$allowed_tags_student['caption'] = array();
$allowed_tags_student['caption']['align'] = array();
// CODE
$allowed_tags_student['code'] = array();
// COL
$allowed_tags_student['col'] = array();
$allowed_tags_student['col']['align'] = array();
$allowed_tags_student['col']['char'] = array();
$allowed_tags_student['col']['charoff'] = array();
$allowed_tags_student['col']['valign'] = array();
$allowed_tags_student['col']['width'] = array();
// DEL
$allowed_tags_student['del'] = array();
$allowed_tags_student['del']['datetime'] = array();
// DD
$allowed_tags_student['dd'] = array();
// DIV
$allowed_tags_student['div'] = array();
$allowed_tags_student['div']['align'] = array();
$allowed_tags_student['div']['style'] = array();
// DL
$allowed_tags_student['dl'] = array();
// DT
$allowed_tags_student['dt'] = array();
// EM
$allowed_tags_student['em'] = array();
// EMBED
$allowed_tags_student['embed'] = array();
$allowed_tags_student['embed']['height'] = array();
$allowed_tags_student['embed']['type'] = array();
$allowed_tags_student['embed']['quality'] = array();
$allowed_tags_student['embed']['src'] = array();
$allowed_tags_student['embed']['width'] = array();

// FIELDSET
$allowed_tags_student['fieldset'] = array();
// FONT
$allowed_tags_student['font'] = array();
$allowed_tags_student['font']['color'] = array();
$allowed_tags_student['font']['face'] = array();
$allowed_tags_student['font']['size'] = array();
$allowed_tags_student['font']['style'] = array();
// FORM
$allowed_tags_student['form'] = array();
$allowed_tags_student['form']['action'] = array();
$allowed_tags_student['form']['accept'] = array();
$allowed_tags_student['form']['accept-charset'] = array();
$allowed_tags_student['form']['enctype'] = array();
$allowed_tags_student['form']['method'] = array();
$allowed_tags_student['form']['name'] = array();
$allowed_tags_student['form']['target'] = array();
// FRAME
$allowed_tags_student_full_page['frame'] = array();
$allowed_tags_student_full_page['frame']['frameborder'] = array();
$allowed_tags_student_full_page['frame']['longsesc'] = array();
$allowed_tags_student_full_page['frame']['marginheight'] = array();
$allowed_tags_student_full_page['frame']['marginwidth'] = array();
$allowed_tags_student_full_page['frame']['name'] = array();
$allowed_tags_student_full_page['frame']['noresize'] = array();
$allowed_tags_student_full_page['frame']['scrolling'] = array();
$allowed_tags_student_full_page['frame']['src'] = array();
// FRAMESET
$allowed_tags_student_full_page['frameset'] = array();
$allowed_tags_student_full_page['frameset']['cols'] = array();
$allowed_tags_student_full_page['frameset']['rows'] = array();
// HEAD
$allowed_tags_student_full_page['head'] = array();
$allowed_tags_student_full_page['head']['profile'] = array();
// H1
$allowed_tags_student['h1'] = array();
$allowed_tags_student['h1']['align'] = array();
// H2
$allowed_tags_student['h2'] = array();
$allowed_tags_student['h2']['align'] = array();
// H3
$allowed_tags_student['h3'] = array();
$allowed_tags_student['h3']['align'] = array();
// H4
$allowed_tags_student['h4'] = array();
$allowed_tags_student['h4']['align'] = array();
// H5
$allowed_tags_student['h5'] = array();
$allowed_tags_student['h5']['align'] = array();
// H6
$allowed_tags_student['h6'] = array();
$allowed_tags_student['h6']['align'] = array();
// HR
$allowed_tags_student['hr'] = array();
$allowed_tags_student['hr']['align'] = array();
$allowed_tags_student['hr']['noshade'] = array();
$allowed_tags_student['hr']['size'] = array();
$allowed_tags_student['hr']['width'] = array();
// HTML
$allowed_tags_student_full_page['html'] = array();
$allowed_tags_student_full_page['html']['xmlns'] = array();
// I
$allowed_tags_student['i'] = array();
// IFRAME
$allowed_tags_student['iframe'] = array();
$allowed_tags_student['iframe']['align'] = array();
$allowed_tags_student['iframe']['frameborder'] = array();
$allowed_tags_student['iframe']['height'] = array();
$allowed_tags_student['iframe']['londesc'] = array();
$allowed_tags_student['iframe']['marginheight'] = array();
$allowed_tags_student['iframe']['marginwidth'] = array();
$allowed_tags_student['iframe']['name'] = array();
$allowed_tags_student['iframe']['scrolling'] = array();
$allowed_tags_student['iframe']['src'] = array();
$allowed_tags_student['iframe']['width'] = array();
// IMG
$allowed_tags_student['img'] = array();
$allowed_tags_student['img']['alt'] = array();
$allowed_tags_student['img']['align'] = array();
$allowed_tags_student['img']['border'] = array();
$allowed_tags_student['img']['height'] = array();
$allowed_tags_student['img']['hspace'] = array();
$allowed_tags_student['img']['ismap'] = array();
$allowed_tags_student['img']['longdesc'] = array();
$allowed_tags_student['img']['src'] = array();
$allowed_tags_student['img']['usemap'] = array();
$allowed_tags_student['img']['vspace'] = array();
$allowed_tags_student['img']['width'] = array();
// INPUT
$allowed_tags_student['input'] = array();
$allowed_tags_student['input']['accept'] = array();
$allowed_tags_student['input']['align'] = array();
$allowed_tags_student['input']['alt'] = array();
$allowed_tags_student['input']['checked'] = array();
$allowed_tags_student['input']['disabled'] = array();
$allowed_tags_student['input']['maxlength'] = array();
$allowed_tags_student['input']['name'] = array();
$allowed_tags_student['input']['readonly'] = array();
$allowed_tags_student['input']['size'] = array();
$allowed_tags_student['input']['src'] = array();
$allowed_tags_student['input']['type'] = array();
$allowed_tags_student['input']['value'] = array();
// INS
$allowed_tags_student['ins'] = array();
$allowed_tags_student['ins']['datetime'] = array();
$allowed_tags_student['ins']['cite'] = array();
// KBD
$allowed_tags_student['kbd'] = array();
// LABEL
$allowed_tags_student['label'] = array();
$allowed_tags_student['label']['for'] = array();
// LEGEND
$allowed_tags_student['legend'] = array();
$allowed_tags_student['legend']['align'] = array();
// LI
$allowed_tags_student['li'] = array();
// LINK
$allowed_tags_student_full_page['link'] = array();
$allowed_tags_student_full_page['link']['charset'] = array();
$allowed_tags_student_full_page['link']['href'] = array();
$allowed_tags_student_full_page['link']['hreflang'] = array();
$allowed_tags_student_full_page['link']['media'] = array();
$allowed_tags_student_full_page['link']['rel'] = array();
$allowed_tags_student_full_page['link']['rev'] = array();
$allowed_tags_student_full_page['link']['target'] = array();
$allowed_tags_student_full_page['link']['type'] = array();
// MAP
$allowed_tags_student['map'] = array();
$allowed_tags_student['map']['id'] = array();
$allowed_tags_student['map']['name'] = array();
// MENU
$allowed_tags_student['menu'] = array();
// META
$allowed_tags_student_full_page['meta'] = array();
$allowed_tags_student_full_page['meta']['content'] = array();
$allowed_tags_student_full_page['meta']['http-equiv'] = array();
$allowed_tags_student_full_page['meta']['name'] = array();
$allowed_tags_student_full_page['meta']['scheme'] = array();
// NOFRAMES
$allowed_tags_student_full_page['noframes'] = array();
// OBJECT
$allowed_tags_student['object'] = array();
$allowed_tags_student['object']['align'] = array();
$allowed_tags_student['object']['archive'] = array();
$allowed_tags_student['object']['border'] = array();
$allowed_tags_student['object']['classid'] = array();
$allowed_tags_student['object']['codebase'] = array();
$allowed_tags_student['object']['codetype'] = array();
$allowed_tags_student['object']['data'] = array();
$allowed_tags_student['object']['declare'] = array();
$allowed_tags_student['object']['height'] = array();
$allowed_tags_student['object']['hspace'] = array();
$allowed_tags_student['object']['name'] = array();
$allowed_tags_student['object']['standby'] = array();
$allowed_tags_student['object']['type'] = array();
$allowed_tags_student['object']['usemap'] = array();
$allowed_tags_student['object']['vspace'] = array();
$allowed_tags_student['object']['width'] = array();
// OL
$allowed_tags_student['ol'] = array();
$allowed_tags_student['ol']['compact'] = array();
$allowed_tags_student['ol']['start'] = array();
$allowed_tags_student['ol']['type'] = array();
// OPTGROUP
$allowed_tags_student['optgroup'] = array();
$allowed_tags_student['optgroup']['label'] = array();
$allowed_tags_student['optgroup']['disabled'] = array();
// OPTION
$allowed_tags_student['option'] = array();
$allowed_tags_student['option']['disabled'] = array();
$allowed_tags_student['option']['label'] = array();
$allowed_tags_student['option']['selected'] = array();
$allowed_tags_student['option']['value'] = array();
// P
$allowed_tags_student['p'] = array();
$allowed_tags_student['p']['align'] = array();
// PARAM
$allowed_tags_student['param'] = array();
$allowed_tags_student['param']['name'] = array();
$allowed_tags_student['param']['type'] = array();
$allowed_tags_student['param']['value'] = array();
$allowed_tags_student['param']['valuetype'] = array();
// PRE
$allowed_tags_student['pre'] = array();
$allowed_tags_student['pre']['width'] = array();
// Q
$allowed_tags_student['q'] = array();
$allowed_tags_student['q']['cite'] = array();
// S
$allowed_tags_student['s'] = array();
// SPAN
$allowed_tags_student['span'] = array();
$allowed_tags_student['span']['style'] = array();
// STRIKE
$allowed_tags_student['strike'] = array();
// STRONG
$allowed_tags_student['strong'] = array();
// STYLE
$allowed_tags_student_full_page['style'] = array();
$allowed_tags_student_full_page['style']['type'] = array();
$allowed_tags_student_full_page['style']['media'] = array();
// SUB
$allowed_tags_student['sub'] = array();
// SUP
$allowed_tags_student['sup'] = array();
// TABLE
$allowed_tags_student['table'] = array();
$allowed_tags_student['table']['align'] = array();
$allowed_tags_student['table']['bgcolor'] = array();
$allowed_tags_student['table']['border'] = array();
$allowed_tags_student['table']['cellpadding'] = array();
$allowed_tags_student['table']['cellspacing'] = array();
$allowed_tags_student['table']['frame'] = array();
$allowed_tags_student['table']['rules'] = array();
$allowed_tags_student['table']['summary'] = array();
$allowed_tags_student['table']['width'] = array();
// TBODY
$allowed_tags_student['tbody'] = array();
$allowed_tags_student['tbody']['align'] = array();
$allowed_tags_student['tbody']['char'] = array();
$allowed_tags_student['tbody']['charoff'] = array();
$allowed_tags_student['tbody']['valign'] = array();
// TD
$allowed_tags_student['td'] = array();
$allowed_tags_student['td']['abbr'] = array();
$allowed_tags_student['td']['align'] = array();
$allowed_tags_student['td']['axis'] = array();
$allowed_tags_student['td']['bgcolor'] = array();
$allowed_tags_student['td']['char'] = array();
$allowed_tags_student['td']['charoff'] = array();
$allowed_tags_student['td']['colspan'] = array();
$allowed_tags_student['td']['headers'] = array();
$allowed_tags_student['td']['height'] = array();
$allowed_tags_student['td']['nowrap'] = array();
$allowed_tags_student['td']['rowspan'] = array();
$allowed_tags_student['td']['scope'] = array();
$allowed_tags_student['td']['valign'] = array();
$allowed_tags_student['td']['width'] = array();
// TEXTAREA
$allowed_tags_student['textarea'] = array();
$allowed_tags_student['textarea']['cols'] = array();
$allowed_tags_student['textarea']['rows'] = array();
$allowed_tags_student['textarea']['disabled'] = array();
$allowed_tags_student['textarea']['name'] = array();
$allowed_tags_student['textarea']['readonly'] = array();
// TFOOT
$allowed_tags_student['tfoot'] = array();
$allowed_tags_student['tfoot']['align'] = array();
$allowed_tags_student['tfoot']['char'] = array();
$allowed_tags_student['tfoot']['charoff'] = array();
$allowed_tags_student['tfoot']['valign'] = array();
// TH
$allowed_tags_student['th'] = array();
$allowed_tags_student['th']['abbr'] = array();
$allowed_tags_student['th']['align'] = array();
$allowed_tags_student['th']['axis'] = array();
$allowed_tags_student['th']['bgcolor'] = array();
$allowed_tags_student['th']['char'] = array();
$allowed_tags_student['th']['charoff'] = array();
$allowed_tags_student['th']['colspan'] = array();
$allowed_tags_student['th']['headers'] = array();
$allowed_tags_student['th']['height'] = array();
$allowed_tags_student['th']['nowrap'] = array();
$allowed_tags_student['th']['rowspan'] = array();
$allowed_tags_student['th']['scope'] = array();
$allowed_tags_student['th']['valign'] = array();
$allowed_tags_student['th']['width'] = array();
// THEAD
$allowed_tags_student['thead'] = array();
$allowed_tags_student['thead']['align'] = array();
$allowed_tags_student['thead']['char'] = array();
$allowed_tags_student['thead']['charoff'] = array();
$allowed_tags_student['thead']['valign'] = array();
// TITLE
$allowed_tags_student['title'] = array();
// TR
$allowed_tags_student['tr'] = array();
$allowed_tags_student['tr']['align'] = array();
$allowed_tags_student['tr']['bgcolor'] = array();
$allowed_tags_student['tr']['char'] = array();
$allowed_tags_student['tr']['charoff'] = array();
$allowed_tags_student['tr']['valign'] = array();
// TT
$allowed_tags_student['tt'] = array();
// U
$allowed_tags_student['u'] = array();
// UL
$allowed_tags_student['ul'] = array();
// VAR
$allowed_tags_student['var'] = array();
//============================================================
// ALLOWED HTML FOR TEACHERS
//============================================================
// Allow all HTML allowed for students
$allowed_tags_teacher = $allowed_tags_student;
// NOSCRIPT
$allowed_tags_teacher['noscript'] = array();
// SCRIPT
$allowed_tags_teacher['script'] = array();
$allowed_tags_teacher['script']['type'] = array();
$allowed_tags_teacher_full_page = $allowed_tags_student_full_page;
?>
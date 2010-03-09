/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//config.removePlugins = 'flash';
	config.extraPlugins = 'chamiloflash,chamiloyoutube,chamilovideo,chamiloaudio';
	config.removePlugins = 'elementspath,resize';
	config.menu_groups = config.menu_groups + ',chamiloflash,chamiloyoutube,chamilovideo,chamiloaudio';
	
	config.toolbar_Full =
		[
		    ['Source','-','Save','NewPage','Preview','-','Templates'],
		    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
		    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
		    '/',
		    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		    ['Link','Unlink','Anchor'],
		    ['Image','Chamiloflash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		    '/',
		    ['Styles','Format','Font','FontSize'],
		    ['TextColor','BGColor'],
		    ['Maximize', 'ShowBlocks','-','About']
		];
	
	config.toolbar_Html =
		[
		 	['Maximize','-','Font','FontSize','Format','Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','Cut','Copy','Paste','PasteText','PasteFromWord'],
		 	'/',
		 	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','-','Outdent','Indent','Blockquote','-','TextColor','BGColor','-','HorizontalRule','Link','Unlink','-','Image','Chamiloflash','Table','-','Source']
		];
	
	config.toolbar_Basic =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Chamiloflash','Chamiloyoutube','Chamilovideo','Chamiloaudio','-','Templates']
		];
	
	config.toolbar_WikiPage =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Chamiloflash','-','Templates']
		];

	config.toolbar_RepositoryQuestion =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor','-','Image','Chamiloflash']
		] ;

	config.toolbar_Assessment =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor']
		] ;
	
	config.filebrowserImageBrowseUrl = 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=image&repoviewer_action=browser';
	config.filebrowserChamiloflashBrowseUrl = 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=flash&repoviewer_action=browser';
	config.filebrowserChamiloyoutubeBrowseUrl = 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=youtube&repoviewer_action=browser';
	config.filebrowserChamilovideoBrowseUrl = 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=video&repoviewer_action=browser';
	config.filebrowserChamiloaudioBrowseUrl = 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=audio&repoviewer_action=browser';

};

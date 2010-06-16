/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
    config.extraPlugins = 'chamiloflash,chamiloyoutube,chamilovideo,chamiloaudio,chamilodailymotion,chamilovimeo';
	config.removePlugins = 'flash,elementspath,resize';
	config.menu_groups = config.menu_groups + ',chamiloflash,chamiloyoutube,chamilovideo,chamiloaudio,chamilodailymotion,chamilovimeo';
	
	
	
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
		 	['Maximize','-','Styles','Format','Font','FontSize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Chamiloflash','Chamiloyoutube','Chamilodailymotion','Chamilovimeo','Chamilovideo','Chamiloaudio','-','Templates']
		];
	
	config.toolbar_BasicMarkup =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule']
		];
	
	config.toolbar_WikiPage =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Chamiloflash','-','Templates']
		];

	config.toolbar_RepositoryQuestion =
		[
		 	['Maximize','PasteFromWord','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor','-','Image','Chamiloflash']
		] ;
		
	config.toolbar_RepositorySurveyQuestion =
		[
		 	['Maximize','PasteFromWord','-','Bold','Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList', 'BulletedList','-','TextColor','BGColor']
		] ;

	config.toolbar_Assessment =
		[
		 	['Maximize','-','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor']
		] ;
	
	config.filebrowserImageBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=image&repoviewer_action=browser';
	config.filebrowserChamiloflashBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=flash&repoviewer_action=browser';
	config.filebrowserChamiloyoutubeBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=youtube&repoviewer_action=browser';
	config.filebrowserChamilovideoBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=video&repoviewer_action=browser';
	config.filebrowserChamiloaudioBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=audio&repoviewer_action=browser';
	config.filebrowserChamilodailymotionBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=dailymotion&repoviewer_action=browser';
	config.filebrowserChamilovimeoBrowseUrl = web_path + 'common/launcher/index.php?application=html_editor_file&plugin=vimeo&repoviewer_action=browser';	
	
	
};

/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
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
		    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		    '/',
		    ['Styles','Format','Font','FontSize'],
		    ['TextColor','BGColor'],
		    ['Maximize', 'ShowBlocks','-','About']
		];

	config.toolbar_Basic =
		[
		    ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];
	
	config.toolbar_Basic =
		[
		 	['Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Flash','-','Templates']
		];
	
	config.toolbar_WikiPage =
		[
		 	['Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Flash','-','Templates']
		];

	config.toolbar_RepositoryQuestion =
		[
		 	['Maximize','Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor','-','Image','Flash']
		] ;

	config.toolbar_Assessment =
		[
		 	['Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','TextColor','BGColor']
		] ;

};

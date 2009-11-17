/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2008 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 * Author: Juan Carlos Raña Trabado
 * Plugin to insert "Wikilinks"  in the editor, based in "Placeholders"
 */

// Register the related command.
FCKCommands.RegisterCommand( 'WikiLink', new FCKDialogCommand(FCKLang['DlgWikiLinkTitle'], FCKLang['DlgWikiLinkTitle'], FCKConfig.PluginsPath + 'wikilink/fck_wikilink.html', 350, 250 ) ) ;

// Create the "YouTube" toolbar button.
var oFindItem		= new FCKToolbarButton( 'WikiLink', FCKLang['WikiLinkTip'] ) ;
oFindItem.IconPath	= FCKConfig.SkinPath + 'wikipedia.gif' ;


FCKToolbarItems.RegisterItem( 'WikiLink', oFindItem ) ;


// The object used for all Placeholder operations.
var FCKPlaceholders = new Object() ;

// Add a new placeholder at the actual selection.
FCKPlaceholders.Add = function( name )
{
	var oSpan = FCK.InsertElement( 'span' ) ;
	this.SetupSpan( oSpan, name ) ;
}

FCKPlaceholders.SetupSpan = function( span, name )
{
	span.innerHTML = '[[' + name + ']]' ;

	if ( FCKBrowserInfo.IsGecko )
		span.style.cursor = 'default' ;

	span._fckplaceholder = name ;
	//span.contentEditable = false ;

	// To avoid it to be resized.
	span.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}
/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{
	var chamilovimeoFilenameRegex = /\.vimeo\./,
		numberRegex = /^\d+(?:\.\d+)?$/;

	function cssifyLength( length )
	{
		if ( numberRegex.test( length ) )
			return length + 'px';
		return length;
	}

	function isChamilovimeoEmbed( element )
	{
		var attributes = element.attributes;

		return chamilovimeoFilenameRegex.test( attributes.src );
	}

	function createFakeElement( editor, realElement )
	{
		var fakeElement = editor.createFakeParserElement( realElement, 'cke_chamilovimeo', 'chamilovimeo', true ),
			fakeStyle = fakeElement.attributes.style || '';

		var width = realElement.attributes.width,
			height = realElement.attributes.height;

		if ( typeof width != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:' + cssifyLength( width ) + ';';

		if ( typeof height != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:' + cssifyLength( height ) + ';';

		return fakeElement;
	}

	CKEDITOR.plugins.add( 'chamilovimeo',
	{
		init : function( editor )
		{
			editor.addCommand( 'chamilovimeo', new CKEDITOR.dialogCommand( 'chamilovimeo' ) );
			editor.ui.addButton( 'Chamilovimeo',
				{
					label : editor.lang.common.chamilovimeo,
					command : 'chamilovimeo',
					icon: this.path + 'chamilovimeo.png'
				});
			CKEDITOR.dialog.add( 'chamilovimeo', this.path + 'dialogs/chamilovimeo.js' );

			editor.addCss(
				'img.cke_chamilovimeo' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'border: 1px solid #a9a9a9;' +
					'width: 80px;' +
					'height: 80px;' +
				'}'
				);

			// If the "menu" plugin is loaded, register the menu items.
			if ( editor.addMenuItems )
			{
				editor.addMenuItems(
					{
						chamilovimeo :
						{
							label : editor.lang.chamilovimeo.properties,
							command : 'chamilovimeo',
							group : 'chamilovimeo'
						}
					});
			}

			// If the "contextmenu" plugin is loaded, register the listeners.
			if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
					{
						if ( element && element.is( 'img' ) && element.getAttribute( '_cke_real_element_type' ) == 'chamilovimeo' )
							return { chamilovimeo : CKEDITOR.TRISTATE_OFF };
					});
			}
		},

		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter;

			if ( dataFilter )
			{
				dataFilter.addRules(
					{
						elements :
						{
							'cke:object' : function( element )
							{
								var attributes = element.attributes,
									classId = attributes.classid && String( attributes.classid ).toLowerCase();

								if ( !classId )
								{
									// Look for the inner <embed>
									for ( var i = 0 ; i < element.children.length ; i++ )
									{
										if ( element.children[ i ].name == 'cke:embed' )
										{
											if ( !isChamilovimeoEmbed( element.children[ i ] ) )
												return null;

											return createFakeElement( editor, element );
										}
									}
									return null;
								}
								
								if ( isChamilovimeoEmbed( element ) )
									return createFakeElement( editor, element );
							},

							'cke:embed' : function( element )
							{
								if (isChamilovimeoEmbed( element ) )
									return createFakeElement( editor, element );
							}
						}
					},
					5);
			}
		},

		requires : [ 'fakeobjects' ]
	});
})();

CKEDITOR.tools.extend( CKEDITOR.config,
{
	/**
	 * Save as EMBED tag only. This tag is unrecommended.
	 * @type Boolean
	 * @default false
	 */
	chamilovimeoEmbedTagOnly : false,

	/**
	 * Add EMBED tag as alternative: &lt;object&gt&lt;embed&gt&lt;/embed&gt&lt;/object&gt
	 * @type Boolean
	 * @default false
	 */
	chamilovimeoAddEmbedTag : true,

	/**
	 * Use embedTagOnly and addEmbedTag values on edit.
	 * @type Boolean
	 * @default false
	 */
	chamilovimeoConvertOnEdit : false
} );

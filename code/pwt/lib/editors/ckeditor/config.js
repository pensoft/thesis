/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};


CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'FullToolbar';
 
	config.toolbar_FullToolbar =
	[				                		
	 	{ name: 'tools', items : [ 'Maximize'] },
	 	'/',
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
		                                 'BGColor','-',
		                                 'Undo','-','Redo', 'autosave'
		                                 ] },
		'/',
		{ name: 'addcontrols', items : [ 'Fig', 'Tbls', 'Refs' ] }
		
	];
	
		
	config.toolbar_FullToolbarNoMaximize =
	[				                		
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
		                                 'BGColor','-',
		                                 'Undo','-','Redo', 'autosave'
		                                 ] },
		'/',
		{ name: 'addcontrols', items : [ 'Fig', 'Tbls', 'Refs' ] }
		
	];
		
	config.toolbar_ModerateToolbar =
		[				                				 	
			{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
			                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
			                                 'BGColor','-',
			                                 'Undo','-','Redo', 'autosave'
			                                 ] },
			
		];
		
	config.toolbar_ModerateTableToolbar =
		[				                				 	
			{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
			                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
			                                 'BGColor','-',
			                                 'Undo','-','Redo','-','Table', 'autosave'
			                                 ] },
			
		];
											 
	config.toolbar_SmallToolbar =
	[				                			 	
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',	         
	         'Undo','-','Redo', 'autosave'
         ] },
		
	];
											 
	config.toolbar_ReferenceCitationToolbar =
	[				                			 	
		{ name: 'basicstyles', items : [ 'autosave' ] },
		{ name: 'addcontrols', items : [ 'Refs' ] }
	];
	
	config.contentsCss = '/lib/css/editor_iframe.css';
	
	config.toolbarCanCollapse = false;
	
	config.bodyClass = 'contents';
	
	config.entities_processNumerical = true;
	
	config.fullPage = false;

};
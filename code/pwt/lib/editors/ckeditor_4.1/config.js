/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};


CKEDITOR.editorConfig = function( config )
{
	config.skin='moono';

	config.toolbar = 'FullToolbar';
	
	config.toolbar_EmptyToolbar = [];
	
	config.toolbar_FullToolbar =
	[
	 	{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-',
		                                 'Outdent', '-', 'Indent', '-', 'Undo','-','Redo', 'autosave'
		                                 ] },
		'/',
		{ name: 'addcontrols', items : [ 'Fig', '-', 'Tbls', '-', 'Refs', '-', 'SupFiles'] },
		'/',
		{ name: 'tools', items : [ 'Maximize'] }

	];
		


	config.toolbar_FullToolbarNoMaximize =
	[
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-',
		                                 'Outdent', '-', 'Indent','-', 'Undo','-','Redo', 'autosave'
		                                 ] },
		'/',
		{ name: 'addcontrols', items : [ 'Fig','-', 'Tbls', '-', 'Refs', '-', 'SupFiles'] }

	];

	config.toolbar_ModerateToolbar =
		[
			{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
			                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-',
			                                 'Outdent', '-', 'Indent', '-', 'Undo','-','Redo', 'autosave'
			                                 ] }

		];

	config.toolbar_ModerateTableToolbar =
		[
			{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
			                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-',
			                                 'Outdent', '-', 'Indent','-', 'Undo','-','Redo','-','Table', 'autosave'
			                                 ] }

		];

	config.toolbar_SmallToolbar =
	[
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
	         'Undo','-','Redo', 'autosave'
         ] }

	];

	config.toolbar_ReferenceCitationToolbar =
	[
		{ name: 'basicstyles', items : [ 'autosave' ] },
		{ name: 'addcontrols', items : [ 'Refs' ] }
	];
			
	config.floatingtools_Basic =[		
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link']
	];

	config.contentsCss = '/lib/css/editor_iframe.css';

	config.toolbarCanCollapse = false;

	config.bodyClass = 'contents';

	config.entities_processNumerical = true;

	config.fullPage = false;

	config.enterMode = CKEDITOR.ENTER_P;

};

CKEDITOR.htmlDataProcessor.prototype.toHtml = function( data, context, fixForBody, dontFilter ){
	//Convert empty br tags to self closing br tags because CKEditor
//	console.log('Before ' + data);
	data = data.replace(/<br(\s[^>]*)?>.*?<\/br>/g, '<br/>');
	var editor = this.editor;
//	console.log('After	 ' + data);

	// Fall back to the editable as context if not specified.
	if ( !context && context !== null )
		context = editor.editable().getName();

	return editor.fire( 'toHtml', {
		dataValue: data,
		context: context,
		fixForBody: fixForBody,
		dontFilter: !!dontFilter
	} ).dataValue;
};
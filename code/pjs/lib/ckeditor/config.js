CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'FullToolbar';
 
	config.toolbar_FullToolbar =
	[				                		
	 	{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-',
										 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
		                                 'TextColor','-','BGColor','-','RemoveFormat','-','Undo','-','Redo'
		                                 ] },
		'/',
		{ name: 'clipboard', items : [ 
										'Cut','-','Copy','-','Paste','-','PasteText','-','PasteFromWord' ] },
		
		{ name: 'document', items : [ 
										'Preview','-','Image','-','Table','-','-','Source' ] },
		'/',
		{ name:'styles', items:[
									'Styles','Format','Font','FontSize'] },
	];
	
	config.toolbar_SmallToolbar =
	[				                			 	
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',	         
	         'Undo','-','Redo', 'autosave'
         ] },
		
	];
	config.toolbar_ModerateToolbar =
		[				                				 	
			{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
			                                 'NumberedList','-','BulletedList','-', 'SpecialChar','-', 'Link','-','Unlink','-','autosave'
											] },
		];
		 
	config.toolbar_BasicStylesToolbar =
	[				                			 	
		{ name: 'basicstyles', items : [ 'Bold','-','Italic','-','Underline','-','Subscript','-','Superscript','-',
		                                 'NumberedList','-','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-',
										 'SpecialChar','-', 'Link','-','Unlink','-', 'Blockquote','-',
		                                 'TextColor','-','BGColor','-','RemoveFormat','-','Undo','-','Redo'
		                                 ] },
		
	];
	
	config.contentsCss = '/lib/editor_iframe.css';

	config.toolbarCanCollapse = false;

	config.bodyClass = 'contents';
	
	config.entities_processNumerical = true;

	config.fullPage = false;
	//config.extraPlugins = 'imgs';
	config.language = 'en';

};
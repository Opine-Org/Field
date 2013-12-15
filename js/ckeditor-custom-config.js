CKEDITOR.editorConfig = function( config ) {
	config.toolbar = [
	   	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	   	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'] },
	   	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	   	'/',
	   	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	   	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	   	{ name: 'others', items: [ '-' ] },
	   	{ name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
	   	'/',
	   	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
	   	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	   	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	   	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
	  ];
};
/*
  Custom CKEditor settings for GCMS
*/

CKEDITOR.editorConfig = function( config )
{
	config.entities = false;
	config.height = 400;
	
	config.toolbar = 
	[
		['Undo','Redo','-','Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','SpellChecker'],
		'/',
		['Styles','Format','Font','-','RemoveFormat'],
		'/',
		['NumberedList','BulletedList','-','Table','HorizontalRule','-','Outdent','Indent','-','Link','Unlink','-','Image','Flash','-','ShowBlocks','Source','-','Maximize','-','About']
	];
	
	config.filebrowserImageUploadUrl = gcms.options.baseUrl + '/admin/upload/image';
};

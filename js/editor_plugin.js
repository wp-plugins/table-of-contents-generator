(function() {
	tinymce.create('tinymce.plugins.TOC', {
		// Plugin initialisation
		init: function(ed, url) {
			// Add command to be fired by button
			ed.addCommand('tinyTOC', function() {
				tinymce.execCommand('mceReplaceContent', false, '[TOC]');
			});
			
			// Add button, hooking to command above
			ed.addButton('tocgenerator', {
				title: 'Insert Table of Contents Shortcode', 
				cmd: 'tinyTOC',
				image: url + '/../images/icons/small.png'
			});
		},
		
		// Plugin info
		getInfo: function() {
			return {
				longname: 'Table of Contents Shortcode',
				author: 'n7 Studios',
				authorurl: 'http://www.n7studios.co.uk',
				infourl: 'http://www.n7studios.co.uk/portfolio/wordpress-table-of-contents-generator',
				version: '1.0'
			};
		}
	});
	
	// Add plugin created above
	tinymce.PluginManager.add('tocgenerator', tinymce.plugins.TOC);
})();
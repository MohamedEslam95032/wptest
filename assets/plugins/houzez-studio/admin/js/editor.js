! function($, elementor){
	"use strict";
	var modules = elementor.modules;

	elementor.on('document:loaded', function() {
		elementor.channels.editor.on('HouzezApplyPreview', saveAndReload);
	});

	function saveAndReload() {
		$e.run('document/save/auto', {
			force: true,
			onSuccess: () => {
				elementor.dynamicTags.cleanCache();
				const isInitialDocument = elementor.config.initial_document.id === elementor.documents.getCurrentId();
				if (isInitialDocument) {
					elementor.reloadPreview();
				} else {
					$e.internal('editor/documents/attach-preview');
				}
			}
		});
	}

}(window.jQuery, window.elementor);
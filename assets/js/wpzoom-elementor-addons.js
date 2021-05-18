(function( $ ) {

	const elementor_add_section_tmpl = $("#tmpl-elementor-add-section");

	if (0 < elementor_add_section_tmpl.length && typeof elementor !== undefined) {
		let text = elementor_add_section_tmpl.text();

		(text = text.replace(
			'<div class="elementor-add-section-drag-title',
			'<div class="elementor-add-section-area-button elementor-add-wpzoom-templates-button" title="WPZOOM Elementor Templates"> <i class="eicon-folder"></i> </div> <div class="elementor-add-section-drag-title'
		)),

		elementor_add_section_tmpl.text(text),
		
		elementor.on( "preview:loaded", function() {
			
			$( elementor.$previewContents[0].body).on(
				"click",
				".elementor-add-wpzoom-templates-button",
				openLibrary
			);
			
		});

		function openLibrary() {

			$e.run( 'library/open');
			elementor.templates.setFilter( 'text', 'WPZOOM' );
			//$( 'body' ).addClass( "wpzoom-elementor-addons-popup-opened" );
		
		}
	
	}

})(jQuery);
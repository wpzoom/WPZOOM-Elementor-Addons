const windowWPZ = window.wpzoom = window.wpzoom || {};
var WPZCached = null;

(function( $ ) {

	const elementor_add_section_tmpl = $( "#tmpl-elementor-add-section" );

	if (0 < elementor_add_section_tmpl.length && typeof elementor !== undefined) {
		let text = elementor_add_section_tmpl.text();

		//Add the WPZOOM Button
		(text = text.replace(
			'<div class="elementor-add-section-drag-title',
			'<div class="elementor-add-section-area-button elementor-add-wpzoom-templates-button" title="WPZOOM Library"> <i class="eicon-folder"></i> </div> <div class="elementor-add-section-drag-title'
		)),

		elementor_add_section_tmpl.text(text),
		elementor.on( "preview:loaded", function() {
			$( elementor.$previewContents[0].body).on(
				"click",
				".elementor-add-wpzoom-templates-button",
				openLibrary
			);
		});

		//Show the loading panel
		function showLoadingView() {
			$('.dialog-lightbox-loading').show();
			$('.dialog-lightbox-content').hide();
		}
		
		//Hide the loading panel
		function hideLoadingView() {
			$('.dialog-lightbox-content').show();
			$('.dialog-lightbox-loading').hide();
		}

		//Create and Open the Lightbox with Templates
		function openLibrary() {
			const insertIndex = 0 < jQuery(this).parents(".elementor-section-wrap").length ? jQuery(this).parents(".elementor-add-section").index() : -1;
			windowWPZ.insertIndex = insertIndex;

			elementorCommon &&
				( windowWPZ.wpzModal ||
					( ( windowWPZ.wpzModal = elementorCommon.dialogsManager.createWidget(
						"lightbox",
						{
							id: "wpzoom-elementor-template-library-modal",
							className: "elementor-templates-modal",
							message: "",
							hide: {
								auto: !1,
								onClick: !1,
								onOutsideClick: !1,
								onOutsideContextMenu: !1,
								onBackgroundClick: !0
							},
							position: {
								my: "center",
								at: "center"
							},
							onShow: function() {
								
								const header = windowWPZ.wpzModal.getElements("header");
								if( !$('.elementor-templates-modal__header').length ) {
									header.append( wp.template( 'wpzoom-elementor-templates-modal__header' ) );
								}
								const content = windowWPZ.wpzModal.getElements("content");
								if( !$('#elementor-template-library-filter-toolbar-remote').length ) {
									content.append( wp.template( 'wpzoom-elementor-template-library-tools' ) );
								}
								if( !$('#wpzoom-elementor-templates-header').length ) {
									content.append('<div id="wpzoom-elementor-templates-header" class="wrap"></div>');
								}
								if( !$('#wpzoom_main_library_templates_panel').length ) {
									content.append('<div id="wpzoom_main_library_templates_panel" class="wpzoom__main-view"></div>');
								}
								if( 'dark' !== elementor.settings.editorPreferences.model.get('ui_theme') ) {
									$("#wpzoom_main_library_templates_panel").removeClass('wpzoom-dark-mode');
								}
								else {
									$("#wpzoom_main_library_templates_panel").addClass('wpzoom-dark-mode');
								}
								const loading = windowWPZ.wpzModal.getElements("loading");
								if( !$('#elementor-template-library-loading').length ) {
									loading.append( wp.template( 'wpzoom-elementor-template-library-loading' ) );
								}
								
								var event = new Event("modal-close");
								$("#wpzoom-elementor-templates").on(
									"click",
									".close-modal",
									function() {
										document.dispatchEvent(event);
										return windowWPZ.wpzModal.hide(), !1;
									}
								);
								$(".elementor-templates-modal__header__close").click( function() {
									return windowWPZ.wpzModal.hide(); 
								});
								wpzoom_get_library_view();
								$('#wpzoom-elementor-template-library-filter-theme').select2({
									placeholder: 'Theme',
									allowClear: true,
									width: 150,
								});
							},
							onHide: function() {
								if( 'dark' !== elementor.settings.editorPreferences.model.get('ui_theme') ) {
									$("#wpzoom_main_library_templates_panel").removeClass('wpzoom-dark-mode');
								}
								else {
									$("#wpzoom_main_library_templates_panel").addClass('wpzoom-dark-mode');
								}
							}
						}
					)),
					windowWPZ.wpzModal.getElements("message").append( windowWPZ.wpzModal.addElement("content"), windowWPZ.wpzModal.addElement('loading') )),
					windowWPZ.wpzModal.show() );
		}

		windowWPZ.wpzModal = null;

	}

    /* Add actions to the WPZOOM view templates panel */
	var WPZupdateActions = function( insertIndex ){

		/* INSERT template buttons */
		$('.wpzoom-btn-template-insert, .elementor-template-library-template-action').unbind('click');
        $('.wpzoom-btn-template-insert, .elementor-template-library-template-action').click(function(){
			var WPZ_selectedElement = this;
            showLoadingView();
			var filename = $( WPZ_selectedElement ).attr( "data-template-name" ) + ".json";
			//console.log(filename);
			$.post( 
				ajaxurl, 
				{ action : 'get_content_from_elementor_export_file', filename: filename }, 
				function(data) {

					data = JSON.parse(data);

					if(insertIndex == -1){
						elementor.getPreviewView().addChildModel(data, {silent: 0});
					} else {
						elementor.getPreviewView().addChildModel(data, {at: insertIndex, silent: 0});
					}
					elementor.channels.data.trigger('template:after:insert', {});
					if (undefined != $e && 'undefined' != typeof $e.internal) {
						$e.internal('document/save/set-is-modified', { status: true })
					}
					else {
						elementor.saver.setFlagEditorChange(true);
					}
					showLoadingView();
					windowWPZ.wpzModal.hide();
			} )
			.fail( function error(errorData) {
				elementor.templates.showErrorDialog( 'The template couldn’t be imported. Please try again or get in touch with the WPZOOM team.' );
				hideLoadingView();
			} );
        });

		/* Filter to show by theme */
		$('#wpzoom-elementor-template-library-filter-theme').on( 'change', function(e) {
            var filters = {};
			$(this).each(function(index, select) {
				var value = String( $(select).val() );
				// if comma separated
				if (value.indexOf(',') !== -1) {
					value = value.split(',');
				}
				filters[$(select).attr('name')] = value;
			});
			$('.wpzoom-item, h2.wpzoom-templates-library-template-category').each(function(i, item) {
				var show = true;
				$.each(filters, function(name, val) {
					if ( val === null ) { return; }
					if ( name === 'theme' && $(item).data('theme').indexOf(val) === -1) {
						show = false;
					} else if( $(item).data(name).indexOf(val) === -1) {
						show = false;
					}
				});
				if (show) {
					$(item).show();
				}else{
					$(item).hide();
				}
			});
			//console.log( this.value );
		});

        /* Open the preview template */
        $('.wpzoom-template-thumb').click( function() {
			var jsonData = $(this).attr('data-template');
			var data = jQuery.parseJSON( jsonData );
			var slug = data.id;
			//console.log( data );
			$('.elementor-templates-modal__header__logo').hide();
			$('#wpzoom-elementor-template-library-toolbar').hide();
			$('#wpzoom-elementor-template-library-header-preview').show();
			$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').attr( 'data-template-name', slug );
			$('.wpzoom-header-back-button').show();
            showLoadingView();
            $.post( ajaxurl, { action : 'get_wpzoom_preview', data: data}, function(data) {
				//console.log( slug );
				hideLoadingView();
				$('.wpzoom__main-view').html( data );
            	WPZupdateActions(insertIndex);
            });
        });

        /* Close preview window */
		$('.wpzoom-header-back-button').click(function() {
			$(this).hide();
			$('#wpzoom-elementor-template-library-header-preview').hide();
			$('#wpzoom-elementor-template-library-toolbar').show();
			$('.elementor-templates-modal__header__logo').show();
			wpzoom_get_library_view();
        });
		
    }

	/* Get all the templates */
	function wpzoom_get_library_view() {
        
		var filters = {};
        if( !insertIndex ) { var insertIndex = null; }

		$('.elementor-templates-modal__header__logo').show();
		$('#wpzoom-elementor-template-library-toolbar').show();
		$('.wpzoom-header-back-button').hide();
		$('#wpzoom-elementor-template-library-header-preview').hide();		

		showLoadingView();
		if( WPZCached == null ) { // If cache not created then load it
			/* Load template view via Ajax */
			$.post( ajaxurl, { action : 'get_wpzoom_templates_library_view' }, function( data ) {

				hideLoadingView();
				$( '.wpzoom__main-view').html( data );
				WPZCached = data;
				WPZupdateActions( insertIndex );
			});
		} else {
			hideLoadingView();
			$('.wpzoom__main-view').html( WPZCached );
			WPZupdateActions( insertIndex );
		}

		//check if filter is not selected
		var filterValue = $('#wpzoom-elementor-template-library-filter-theme').val();
		if( filterValue ) {
			filters['theme'] = filterValue;
			$( '.wpzoom-item, h2.wpzoom-templates-library-template-category' ).each( function( i, item ) {
				var show = true;
				$.each( filters, function( name, val ) {
					if ( val === null ) { return; }
					if ( name === 'theme' && $(item).data('theme').indexOf(val) === -1 ) {
						show = false;
					} else if( $(item).data(name).indexOf(val) === -1) {
						show = false;
					}
				});
				if (show) {
					$(item).show();
				}else{
					$(item).hide();
				}
			});
		}

		/* Add bottom hover to capture the correct index for insertion */
		var getTemplateBottomButton = $('#elementor-preview-iframe').contents().find('#elementor-add-new-section .elementor-add-template-button');
		if( getTemplateBottomButton.length && !getTemplateBottomButton.hasClass('WPZ_template_btn') ){
			getTemplateBottomButton.hover(function(){
				$(this).addClass('WPZ_template_btn');
				insertIndex = -1;
			});
		}

		/* Add inline hover to capture the correct index for insertion */
		var getTemplateInlineButtons = $('#elementor-preview-iframe').contents().find('.elementor-add-section-inline .elementor-add-template-button');
		if( getTemplateInlineButtons.length ){
			getTemplateInlineButtons.each(function(){
				if(!$(this).hasClass('WPZ_template_btn')){
					$(this).addClass('WPZ_template_btn');
				} else {
					$(this).unbind('hover');
					$(this).hover(function(){
						var templateContainer = $(this).parent().parent().parent(),
						allSections = $(this).parent().parent().parent().parent().children(),
						tempInsertIndex = [];
						for (let index = 0; index < allSections.length; index++) {
							if(allSections[index].localName != 'div' || allSections[index] == templateContainer[0]){
								tempInsertIndex.push(allSections[index]);
							}
						} // Make new array with only the selected add template
						for (let index = 0; index < tempInsertIndex.length; index++) {
							if(tempInsertIndex[index] == templateContainer[0]){ insertIndex = index;  }
						} // get index of that selected add template area
					});
				}
			}); /* loop through inline template buttons */

		}  /* Inline template exists */
	}


})(jQuery);
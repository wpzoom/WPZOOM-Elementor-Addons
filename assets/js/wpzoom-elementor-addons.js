const windowWPZ = window.wpzoom = window.wpzoom || {};
var WPZCached = null; // legacy cache (templates)
var WPZCachedTemplates = null;
var WPZCachedSections = null;

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
				((windowWPZ.wpzModal = elementorCommon.dialogsManager.createWidget(
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
							onShow: function () {
								
								const header = windowWPZ.wpzModal.getElements("header");
								if( !$('.elementor-templates-modal__header').length ) {
									header.append( wp.template( 'wpzoom-elementor-templates-modal__header' ) );
								}
								const content = windowWPZ.wpzModal.getElements("content");
								if (!$('#elementor-template-library-filter-toolbar-remote').length) {
									content.append(wp.template('wpzoom-elementor-template-library-tools'));
								}
								// Reset active tab UI to Templates on each open
								$('#wpzoom-elementor-template-library-tabs .elementor-template-library-menu-item').removeClass('elementor-active').attr('aria-selected', 'false');
								$('#wpzoom-elementor-template-library-tabs .elementor-template-library-menu-item[data-tab="templates"]').addClass('elementor-active').attr('aria-selected', 'true');
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
								// init tab state
								windowWPZ.currentTab = 'templates';
								// bind tab switching
								$('#wpzoom-elementor-template-library-tabs').off('click keypress', '.elementor-template-library-menu-item')
									.on('click keypress', '.elementor-template-library-menu-item', function (e) {
										if (e.type === 'keypress' && e.key !== 'Enter' && e.key !== ' ') { return; }
										var $btn = $(this);
										if ($btn.hasClass('elementor-active')) { return; }
										$('#wpzoom-elementor-template-library-tabs .elementor-template-library-menu-item').removeClass('elementor-active').attr('aria-selected', 'false');
										$btn.addClass('elementor-active').attr('aria-selected', 'true');
									windowWPZ.currentTab = $btn.data('tab') === 'sections' ? 'sections' : 'templates';
									wpzoom_get_library_view(windowWPZ.currentTab);
									});

								wpzoom_get_library_view('templates');
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
			// Check if this is a locked template trying to be inserted
			if ( $(this).hasClass('wpzoom-locked-template') ) {
				// Show upgrade notice for locked template insertion
				elementor.templates.showErrorDialog( 'This template is only available with WPZOOM Elementor Addons Pro license. Please visit wpzoom.com to get your license key.' );
				return false;
			}

			var WPZ_selectedElement = this;
            showLoadingView();
			var filename = $( WPZ_selectedElement ).attr( "data-template-name" ) + ".json";
			//console.log(filename);
					$.post( 
			ajaxurl, 
			{ 
				action : 'get_content_from_elementor_export_file', 
				filename: filename
			}, 
			function(data) {
				try {
					// If data is already an object (from wp_send_json_error), use it directly
					if (typeof data === 'object' && data !== null) {
						if (data.success === false) {
							// Handle license error specifically
							if (data.data && data.data.is_license_error) {
								var errorMessage = data.data.message || 'This template requires WPZOOM Elementor Addons Pro license.';
								var licensePageUrl = (typeof wpzoom_admin_data !== 'undefined' && wpzoom_admin_data.license_page_url) ? wpzoom_admin_data.license_page_url : '/wp-admin/options-general.php?page=wpzoom-addons-license';
								var getLicenseUrl = (typeof wpzoom_admin_data !== 'undefined' && wpzoom_admin_data.get_license_url) ? wpzoom_admin_data.get_license_url : 'https://www.wpzoom.com/plugins/wpzoom-elementor-addons/';
								errorMessage += '<br><br><a href="' + licensePageUrl + '" target="_blank" style="color: #007cba; text-decoration: none;">Enter License Key</a> | <a href="' + getLicenseUrl + '" target="_blank" style="color: #007cba; text-decoration: none;">Get License Key</a>';
								elementor.templates.showErrorDialog( errorMessage );
							} else {
								elementor.templates.showErrorDialog( data.data.message || 'The template could not be imported. Please try again.' );
							}
							hideLoadingView();
							return;
						}
					}

					// Parse data if it's a string
					if (typeof data === 'string') {
						data = JSON.parse(data);
					}

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
				} catch (e) {
					console.error('Error parsing template data:', e);
					elementor.templates.showErrorDialog( 'The template could not be imported. Invalid template data.' );
					hideLoadingView();
				}
		} )
		.fail( function error(errorData) {
			var errorMessage = 'The template could not be imported. Please try again or get in touch with the WPZOOM team.';
			
			// Check if it's a license-related error
			if (errorData.responseJSON && errorData.responseJSON.data && errorData.responseJSON.data.is_license_error) {
				errorMessage = errorData.responseJSON.data.message || 'This template requires WPZOOM Elementor Addons Pro license.';
				var licensePageUrl = (typeof wpzoom_admin_data !== 'undefined' && wpzoom_admin_data.license_page_url) ? wpzoom_admin_data.license_page_url : '/wp-admin/options-general.php?page=wpzoom-addons-license';
				var getLicenseUrl = (typeof wpzoom_admin_data !== 'undefined' && wpzoom_admin_data.get_license_url) ? wpzoom_admin_data.get_license_url : 'https://www.wpzoom.com/plugins/wpzoom-elementor-addons/';
				errorMessage += '<br><br><a href="' + licensePageUrl + '" target="_blank" style="color: #007cba; text-decoration: none;">Enter License Key</a> | <a href="' + getLicenseUrl + '" target="_blank" style="color: #007cba; text-decoration: none;">Get License Key</a>';
			}
			
			elementor.templates.showErrorDialog( errorMessage );
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
		$('.wpzoom-template-thumb').click(function () {
			var jsonData = $(this).attr('data-template');
			var data = jQuery.parseJSON( jsonData );
			var rawId = data.id || '';
			var slug = String(rawId).toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '');
			var isLocked = $(this).hasClass('wpzoom-template-thumb-locked');

			//console.log( data );
			$('.elementor-templates-modal__header__logo').hide();
			$('#wpzoom-elementor-template-library-toolbar').hide();
			$('#wpzoom-elementor-template-library-header-preview').show();
			$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').attr( 'data-template-name', slug );

			// If template is locked, add a class to the insert button to handle it differently
			if ( isLocked ) {
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').addClass('wpzoom-locked-template');
				// Update button text and style for locked templates
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-button-title').text('Unlock with Pro');
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').css({
					'background': '#3496ff',
					'color': '#fff'
				});
			} else {
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').removeClass('wpzoom-locked-template');
				// Reset button text and style for free templates
				var insertLabel = (windowWPZ.currentTab === 'sections') ? 'Insert Section' : 'Insert Page';
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-button-title').text(insertLabel);
				$('#wpzoom-elementor-template-library-header-preview').find('.elementor-template-library-template-action').css({
					'background': '',
					'border-color': '',
					'color': ''
				});
			}

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
	function wpzoom_get_library_view(activeTab) {
		var tab = activeTab === 'sections' ? 'sections' : 'templates';
		var filters = {};
		if (!insertIndex) { var insertIndex = null; }

		$('.elementor-templates-modal__header__logo').show();
		$('#wpzoom-elementor-template-library-toolbar').show();
		$('.wpzoom-header-back-button').hide();
		$('#wpzoom-elementor-template-library-header-preview').hide();		

		showLoadingView();
		var action = (tab === 'sections') ? 'get_wpzoom_sections_library_view' : 'get_wpzoom_pages_library_view';
		var cached = (tab === 'sections') ? WPZCachedSections : (WPZCachedTemplates || WPZCached || null);
		if (cached == null) { // If cache not created then load it
			/* Load library view via Ajax */
			$.post(ajaxurl, { action: action }, function (data) {

				hideLoadingView();
				$( '.wpzoom__main-view').html( data );
				if (tab === 'sections') {
					WPZCachedSections = data;
				} else {
					WPZCachedTemplates = data;
					WPZCached = data; // keep legacy cache in sync
				}
				WPZupdateActions( insertIndex );
			});
		} else {
			hideLoadingView();
			$('.wpzoom__main-view').html(cached);
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
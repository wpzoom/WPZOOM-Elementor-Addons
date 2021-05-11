const windowAnalog = window.analog = window.analog || {};

"undefined" != typeof jQuery &&
	!(function($) {
		$(function() {
			function openLibrary() {
				const insertIndex = 0 < jQuery(this).parents(".elementor-section-wrap").length ? jQuery(this).parents(".elementor-add-section").index() : -1;

				windowAnalog.insertIndex = insertIndex;

				elementorCommon &&
					(windowAnalog.analogModal ||
						((windowAnalog.analogModal = elementorCommon.dialogsManager.createWidget(
							"lightbox",
							{
								id: "wpzoom-elementor-templates-modal",
								headerMessage: "What is this???",
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
									const content = windowAnalog.analogModal.getElements("content");
									content.append('<div id="wpzoom-elementor-templates" class="wrap"></div>');
									var event = new Event("modal-close");
									$("#wpzoom-elementor-templates").on(
										"click",
										".close-modal",
										function() {
											document.dispatchEvent(event);
											return windowAnalog.analogModal.hide(), !1;
										}
									);
								},
								onHide: function() {}
							}
						)),
						windowAnalog.analogModal.getElements("header").remove(),
						windowAnalog.analogModal
							.getElements("message")
							.append(windowAnalog.analogModal.addElement("content"))),
					windowAnalog.analogModal.show());
			}

			windowAnalog.analogModal = null;

			const elementor_add_section_tmpl = $("#tmpl-elementor-add-section");

			if (0 < elementor_add_section_tmpl.length && typeof elementor !== undefined) {
				let text = elementor_add_section_tmpl.text();

				(text = text.replace(
					'<div class="elementor-add-section-drag-title',
					'<div class="elementor-add-section-area-button elementor-add-wpzoom-templates-button" title="WPZOOM Elementor Templates"> <i class="eicon-folder"></i> </div> <div class="elementor-add-section-drag-title'
				)),
					elementor_add_section_tmpl.text(text),
					elementor.on("preview:loaded", function() {
						$(elementor.$previewContents[0].body).on(
							"click",
							".elementor-add-wpzoom-templates-button",
							openLibrary
						);
					});
			}
		});
	})(jQuery);
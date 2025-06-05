;(function ($) {
	'use strict';

	var $window = $(window);

	function debounce( func, wait, immediate ) {
		var timeout;

		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( !immediate ) func.apply( context, args );
			};
			var callNow = immediate && !timeout;

			clearTimeout( timeout );

			timeout = setTimeout( later, wait );

			if ( callNow ) func.apply( context, args );
		};
	}

	$window.on( 'elementor/frontend/init', function() {
		var ModuleHandler = elementorModules.frontend.handlers.Base;

		// Global fallback for video lightbox triggers
		$(document).on('click', '.wpz-slick-lightbox-trigger', function(e) {
			// Check if this trigger has already been handled by a widget instance
			if ($(this).data('wpz-lightbox-handled')) {
				return;
			}
			
			e.preventDefault();
			e.stopPropagation();
			
			var videoUrl = $(this).attr('href');
			console.log('Global video lightbox fallback triggered for:', videoUrl);
			
			// Find the closest video slider widget to get the handler
			var $widget = $(this).closest('.elementor-widget-wpzoom-elementor-addons-video-slider');
			if ($widget.length > 0) {
				// Try to get the widget handler and call the lightbox
				var widgetData = $widget.data('wpz-video-slider-handler');
				if (widgetData && widgetData.openVideoLightbox) {
					widgetData.openVideoLightbox(videoUrl);
				} else {
					// Fallback: create a minimal lightbox
					openGlobalVideoLightbox(videoUrl);
				}
			} else {
				// No widget found, use global fallback
				openGlobalVideoLightbox(videoUrl);
			}
		});

		// Global lightbox functionality as fallback
		function openGlobalVideoLightbox(videoUrl) {
			console.log('Opening global video lightbox for:', videoUrl);
			
			var videoType = detectVideoTypeFromUrl(videoUrl);
			var videoId = getVideoId(videoUrl, videoType);
			
			// Create lightbox overlay
			var $lightbox = $('<div class="wpz-video-lightbox"></div>');
			var $overlay = $('<div class="wpz-lightbox-overlay"></div>');
			var $container = $('<div class="wpz-lightbox-container"></div>');
			var $content = $('<div class="wpz-lightbox-content"></div>');
			var $closeBtn = $('<button class="wpz-lightbox-close" aria-label="Close video"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>');
			
			// Build video content
			var videoContent = buildGlobalVideoContent(videoUrl, videoType, videoId);
			
			if (!videoContent) {
				console.error('Unable to create video content for:', videoUrl);
				return;
			}
			
			// Assemble lightbox
			$content.append(videoContent);
			$container.append($closeBtn).append($content);
			$lightbox.append($overlay).append($container);
			
			// Add to DOM
			$('body').append($lightbox).addClass('wpz-lightbox-open');
			
			// Bind close events
			$closeBtn.on('click', function() {
				closeLightbox($lightbox);
			});
			
			$overlay.on('click', function() {
				closeLightbox($lightbox);
			});
			
			// ESC key to close
			$(document).on('keyup.wpzLightbox', function(e) {
				if (e.keyCode === 27) {
					closeLightbox($lightbox);
				}
			});
			
			// Show lightbox with animation
			setTimeout(function() {
				$lightbox.addClass('wpz-lightbox-show');
			}, 10);
		}

		function detectVideoTypeFromUrl(url) {
			if (url.match(/(?:youtube\.com|youtu\.be)/)) {
				return 'youtube';
			} else if (url.match(/vimeo\.com/)) {
				return 'vimeo';
			} else if (url.match(/\.(mp4|webm|ogg)$/i)) {
				return 'hosted';
			}
			return 'unknown';
		}

		function getVideoId(url, type) {
			var id = '';
			
			switch(type) {
				case 'youtube':
					var match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
					id = match ? match[1] : '';
					break;
				case 'vimeo':
					var match = url.match(/(?:vimeo\.com\/)([0-9]+)/);
					id = match ? match[1] : '';
					break;
			}
			
			return id;
		}

		function buildGlobalVideoContent(videoUrl, videoType, videoId) {
			var $videoWrapper = $('<div class="wpz-lightbox-video"></div>');
			
			switch(videoType) {
				case 'youtube':
					if (!videoId) return null;
					var $iframe = $('<iframe></iframe>');
					var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&showinfo=0';
					$iframe.attr({
						src: embedUrl,
						frameborder: 0,
						allow: 'autoplay; fullscreen',
						allowfullscreen: true,
						width: '100%',
						height: '100%'
					});
					$videoWrapper.append($iframe);
					break;
					
				case 'vimeo':
					if (!videoId) return null;
					var $iframe = $('<iframe></iframe>');
					var embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1&title=0&byline=0&portrait=0';
					$iframe.attr({
						src: embedUrl,
						frameborder: 0,
						allow: 'autoplay; fullscreen',
						allowfullscreen: true,
						width: '100%',
						height: '100%'
					});
					$videoWrapper.append($iframe);
					break;
					
				case 'hosted':
					var $video = $('<video controls autoplay></video>');
					$video.attr({
						src: videoUrl,
						width: '100%',
						height: '100%'
					});
					$videoWrapper.append($video);
					break;
					
				default:
					console.error('Unsupported video type:', videoType);
					return null;
			}
			
			return $videoWrapper;
		}

		function closeLightbox($lightbox) {
			$lightbox.removeClass('wpz-lightbox-show');
			$('body').removeClass('wpz-lightbox-open');
			$(document).off('keyup.wpzLightbox');
			
			setTimeout(function() {
				$lightbox.remove();
			}, 300);
		}

		var SliderBase = ModuleHandler.extend( {
			bindEvents: function() {
				this.removeArrows();
				this.run();
			},

			removeArrows: function() {
				var _this = this;

				this.elements.$container.on( 'init', function() {
					_this.elements.$container.siblings().hide();
				} );
			},

			getDefaultSettings: function() {
				return {
					autoplay    : false,
					arrows      : false,
					checkVisible: false,
					container   : '.wpzjs-slick',
					dots        : false,
					infinite    : true,
					rows        : 0,
					slidesToShow: 1,
					prevArrow   : $( '<div />' ).append( this.findElement( '.slick-prev' ).clone().show() ).html(),
					nextArrow   : $( '<div />' ).append( this.findElement( '.slick-next' ).clone().show() ).html(),
					selectors   : {
						videoBg: '.wpz-video-bg',
						videoWrapper: '.wpz-video-wrapper'
					}
				}
			},

			getDefaultElements: function () {
				return {
					$container   : this.findElement( this.getSettings( 'container' ) ),
					$videoBg     : this.$element.find( this.getSettings( 'selectors.videoBg' ) ),
					$videoWrapper: this.$element.find( this.getSettings( 'selectors.videoWrapper' ) )
				};
			},

			onInit: function () {
				ModuleHandler.prototype.onInit.apply( this, arguments );
				
				// Store reference to this handler in the widget element for global fallback
				this.$element.data('wpz-video-slider-handler', this);
				
				// Initialize video lightbox immediately for static elements
				this.initVideoLightbox();
				
				// Initialize video backgrounds and lightbox after slider is ready
				this.elements.$container.on('init', () => {
					this.initVideoBackgrounds();
					// Re-initialize lightbox after slider is ready
					setTimeout(() => {
						this.initVideoLightbox();
					}, 100);
					
					// Ensure first slide video is sized properly
					setTimeout(() => {
						this.resizeCurrentSlideVideo();
					}, 300);
					
					this.initEditorSupport();
				});
				
				// Also initialize after slider is completely ready
				this.elements.$container.on('afterChange', () => {
					setTimeout(() => {
						this.resizeCurrentSlideVideo();
					}, 100);
				});
			},

			resizeCurrentSlideVideo: function() {
				var self = this;
				var $currentSlide = this.elements.$container.find('.slick-current');
				
				if ($currentSlide.length) {
					var $videoBg = $currentSlide.find('.wpz-video-bg');
					if ($videoBg.length) {
						var $video = $videoBg.find('iframe, video');
						if ($video.length) {
							console.log('Resizing current slide video:', $currentSlide.index());
							this.resizeVideoBackground($videoBg, $video);
							
							// For Vimeo/YouTube iframes, retry sizing after a longer delay
							if ($video.is('iframe')) {
								setTimeout(function() {
									self.resizeVideoBackground($videoBg, $video);
								}, 1000);
								
								// Additional retry for stubborn videos
								setTimeout(function() {
									self.resizeVideoBackground($videoBg, $video);
								}, 2000);
							}
						}
					}
				}
			},

			initVideoBackgrounds: function() {
				var self = this;
				
				// Initialize dynamic video sizing first
				this.initDynamicVideoSizing();
				
				this.elements.$videoBg.each(function() {
					var $videoBg = $(this);
					var videoType = $videoBg.data('video-type');
					var videoUrl = $videoBg.data('video-url');
					var playOnMobile = $videoBg.data('play-on-mobile');

					console.log('Video Slider: Initializing video background', {
						type: videoType,
						url: videoUrl,
						mobile: playOnMobile,
						isMobile: self.isMobile()
					});

					// Check if we should play on mobile
					if (!playOnMobile && self.isMobile()) {
						$videoBg.hide();
						console.log('Video Slider: Hiding video on mobile');
						return;
					}

					switch(videoType) {
						case 'youtube':
							self.initYouTubeBackground($videoBg, videoUrl, $videoBg.data());
							break;
						case 'vimeo':
							self.initVimeoBackground($videoBg, videoUrl, $videoBg.data());
							break;
						case 'hosted':
							self.initHostedVideoBackground($videoBg);
							break;
						default:
							console.warn('Video Slider: Unknown video type:', videoType);
					}
				});
			},

			initDynamicVideoSizing: function() {
				var self = this;
				
				// Apply sizing to all video backgrounds in this widget
				this.$element.find('.wpz-video-bg').each(function(index) {
					var $container = $(this);
					var $video = $container.find('iframe, video');
					
					if ($video.length) {
						var isFirstSlide = $container.closest('.slick-slide').hasClass('slick-current') || 
										   $container.closest('.slick-slide').index() === 0;
						
						// Initial sizing with delay (shorter for first slide)
						var initialDelay = isFirstSlide ? 50 : 100;
						setTimeout(function() {
							self.resizeVideoBackground($container, $video);
						}, initialDelay);
						
						// For iframes (YouTube/Vimeo), add progressive retry logic
						if ($video.is('iframe')) {
							var videoType = $container.data('video-type');
							
							// Progressive retry for better reliability
							var retryDelays = isFirstSlide ? [200, 500, 1000, 2000] : [500, 1000];
							
							retryDelays.forEach(function(delay) {
								setTimeout(function() {
									self.resizeVideoBackground($container, $video);
								}, delay);
							});
							
							// Listen for iframe load
							$video.on('load', function() {
								console.log('Iframe loaded for', videoType, 'video, resizing...');
								setTimeout(function() {
									self.resizeVideoBackground($container, $video);
								}, 100);
								
								// Additional delay for Vimeo which can be slow
								if (videoType === 'vimeo') {
									setTimeout(function() {
										self.resizeVideoBackground($container, $video);
									}, 500);
								}
							});
						}
						
						// For hosted videos, wait for metadata
						if ($video.is('video')) {
							$video.on('loadedmetadata', function() {
								setTimeout(function() {
									self.resizeVideoBackground($container, $video);
								}, 100);
							});
						}
					}
				});
				
				// Set up resize handler for this widget only
				var self = this;
				var widgetId = this.$element.attr('data-element_type') + '_' + this.$element.attr('data-id') || Math.random().toString(36).substr(2, 9);
				var resizeHandler = debounce(function() {
					self.$element.find('.wpz-video-bg').each(function() {
						var $container = $(this);
						var $video = $container.find('iframe, video');
						if ($video.length) {
							self.resizeVideoBackground($container, $video);
						}
					});
				}, 100);
				
				$(window).off('resize.wpzvideo' + widgetId)
					.on('resize.wpzvideo' + widgetId, resizeHandler);
			},

			resizeVideoBackground: function($container, $video) {
				// Ensure container has dimensions
				var containerWidth = $container.outerWidth();
				var containerHeight = $container.outerHeight();
				
				// If container doesn't have proper dimensions yet, try parent
				if (containerWidth <= 0 || containerHeight <= 0) {
					var $parent = $container.closest('.wpz-slick-item');
					if ($parent.length) {
						containerWidth = $parent.outerWidth();
						containerHeight = $parent.outerHeight();
					}
				}
				
				// Still no dimensions? Try slider container
				if (containerWidth <= 0 || containerHeight <= 0) {
					var $slider = $container.closest('.wpzjs-slick');
					if ($slider.length) {
						containerWidth = $slider.outerWidth();
						containerHeight = $slider.outerHeight();
					}
				}
				
				// Last resort: use window dimensions
				if (containerWidth <= 0 || containerHeight <= 0) {
					containerWidth = $(window).width();
					containerHeight = $(window).height();
				}
				
				if (containerWidth <= 0 || containerHeight <= 0) {
					console.warn('Unable to determine container dimensions for video background');
					return;
				}
				
				var containerRatio = containerWidth / containerHeight;
				
				// Default video aspect ratio (16:9)
				var videoRatio = 16 / 9;
				
				// Try to get actual video ratio for hosted videos
				var videoType = $container.attr('data-video-type');
				if (videoType === 'hosted' && $video.is('video')) {
					var videoElement = $video[0];
					if (videoElement.videoWidth && videoElement.videoHeight) {
						videoRatio = videoElement.videoWidth / videoElement.videoHeight;
					}
				}
				
				var newWidth, newHeight;
				
				// Calculate dimensions to fill container completely (cover behavior)
				if (containerRatio > videoRatio) {
					// Container is wider relative to its height than video
					// Video needs to fill width and extend beyond height
					newWidth = containerWidth;
					newHeight = containerWidth / videoRatio;
				} else {
					// Container is taller relative to its width than video  
					// Video needs to fill height and extend beyond width
					newHeight = containerHeight;
					newWidth = containerHeight * videoRatio;
				}
				
				// Ensure minimum coverage - video must be at least as large as container
				if (newWidth < containerWidth) {
					newWidth = containerWidth;
					newHeight = containerWidth / videoRatio;
				}
				
				if (newHeight < containerHeight) {
					newHeight = containerHeight;
					newWidth = containerHeight * videoRatio;
				}
				
				// Apply calculated dimensions with center positioning
				$video.css({
					width: newWidth + 'px',
					height: newHeight + 'px',
					position: 'absolute',
					top: '50%',
					left: '50%',
					transform: 'translate(-50%, -50%)',
					maxWidth: 'none', // Override any max-width constraints
					maxHeight: 'none', // Override any max-height constraints
					minWidth: containerWidth + 'px',
					minHeight: containerHeight + 'px'
				});
				
				// For hosted videos, ensure object-fit cover as backup
				if ($video.is('video')) {
					$video.css('object-fit', 'cover');
				}
			},

			initVideoLightbox: function() {
				var self = this;
				
				// Remove any existing event handlers to prevent duplicates
				this.$element.off('click.wpzLightbox', '.wpz-slick-lightbox-trigger');
				
				// Mark all lightbox triggers in this widget as handled
				this.$element.find('.wpz-slick-lightbox-trigger').data('wpz-lightbox-handled', true);
				
				// Use event delegation to handle clicks on lightbox triggers
				this.$element.on('click.wpzLightbox', '.wpz-slick-lightbox-trigger', function(e) {
					e.preventDefault();
					e.stopPropagation();
					
					var $trigger = $(this);
					var videoUrl = $trigger.attr('href');
					
					console.log('Video lightbox trigger clicked (widget handler):', videoUrl);
					
					if (videoUrl) {
						self.openVideoLightbox(videoUrl);
					}
				});
				
				console.log('Video lightbox initialized with event delegation');
			},

			openVideoLightbox: function(videoUrl) {
				var self = this;
				var videoType = this.detectVideoTypeFromUrl(videoUrl);
				var videoId = this.getVideoId(videoUrl, videoType);
				
				console.log('Opening video lightbox:', { url: videoUrl, type: videoType, id: videoId });
				console.log('Video lightbox trigger working in Elementor!');
				
				// Create lightbox overlay
				var $lightbox = $('<div class="wpz-video-lightbox"></div>');
				var $overlay = $('<div class="wpz-lightbox-overlay"></div>');
				var $container = $('<div class="wpz-lightbox-container"></div>');
				var $content = $('<div class="wpz-lightbox-content"></div>');
				var $closeBtn = $('<button class="wpz-lightbox-close" aria-label="Close video"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>');
				
				// Build video content based on type
				var videoContent = this.buildLightboxVideoContent(videoUrl, videoType, videoId);
				
				if (!videoContent) {
					console.error('Unable to create video content for:', videoUrl);
					return;
				}
				
				// Assemble lightbox
				$content.append($closeBtn).append(videoContent);
				$container.append($content);
				$lightbox.append($overlay).append($container);
				
				// Add to DOM
				$('body').append($lightbox).addClass('wpz-lightbox-open');
				
				// Bind close events
				$closeBtn.on('click', function() {
					self.closeLightbox($lightbox);
				});
				
				$overlay.on('click', function() {
					self.closeLightbox($lightbox);
				});
				
				// ESC key to close
				$(document).on('keyup.wpzLightbox', function(e) {
					if (e.keyCode === 27) {
						self.closeLightbox($lightbox);
					}
				});
				
				// Show lightbox with animation
				setTimeout(function() {
					$lightbox.addClass('wpz-lightbox-show');
				}, 10);
			},

			detectVideoTypeFromUrl: function(url) {
				if (url.match(/(?:youtube\.com|youtu\.be)/)) {
					return 'youtube';
				} else if (url.match(/vimeo\.com/)) {
					return 'vimeo';
				} else if (url.match(/\.(mp4|webm|ogg)$/i)) {
					return 'hosted';
				}
				return 'unknown';
			},

			buildLightboxVideoContent: function(videoUrl, videoType, videoId) {
				var $videoWrapper = $('<div class="wpz-lightbox-video"></div>');
				
				switch(videoType) {
					case 'youtube':
						if (!videoId) return null;
						var $iframe = $('<iframe></iframe>');
						var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&showinfo=0';
						$iframe.attr({
							src: embedUrl,
							frameborder: 0,
							allow: 'autoplay; fullscreen',
							allowfullscreen: true,
							width: '100%',
							height: '100%'
						});
						$videoWrapper.append($iframe);
						break;
						
					case 'vimeo':
						if (!videoId) return null;
						var $iframe = $('<iframe></iframe>');
						var embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1&title=0&byline=0&portrait=0';
						$iframe.attr({
							src: embedUrl,
							frameborder: 0,
							allow: 'autoplay; fullscreen',
							allowfullscreen: true,
							width: '100%',
							height: '100%'
						});
						$videoWrapper.append($iframe);
						break;
						
					case 'hosted':
						var $video = $('<video controls autoplay></video>');
						$video.attr({
							src: videoUrl,
							width: '100%',
							height: '100%'
						});
						$videoWrapper.append($video);
						break;
						
					default:
						console.error('Unsupported video type:', videoType);
						return null;
				}
				
				return $videoWrapper;
			},

			closeLightbox: function($lightbox) {
				$lightbox.removeClass('wpz-lightbox-show');
				$('body').removeClass('wpz-lightbox-open');
				$(document).off('keyup.wpzLightbox');
				
				setTimeout(function() {
					$lightbox.remove();
				}, 300);
			},

			initEditorSupport: function() {
				// Only run in Elementor editor
				if (!elementorFrontend.isEditMode()) {
					return;
				}

				var self = this;
				
				// Listen for panel changes in editor
				elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
					if (model.get('widgetType') === 'wpzoom-elementor-addons-video-slider') {
						// Get the widget element
						var $widget = view.$el;
						var $slider = $widget.find('.wpzjs-slick');
						
						if ($slider.length && $slider.hasClass('slick-initialized')) {
							// Listen for repeater item focus
							setTimeout(function() {
								$('.elementor-repeater-fields').on('click', function() {
									var $repeaterItem = $(this);
									var itemIndex = $repeaterItem.index();
									
									// Go to the corresponding slide
									if (itemIndex >= 0) {
										$slider.slick('slickGoTo', itemIndex);
									}
								});
							}, 500);
						}
					}
				});
			},

			isMobile: function() {
				return window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
			},

			getVideoId: function(url, type) {
				var id = '';
				
				switch(type) {
					case 'youtube':
						var match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
						id = match ? match[1] : '';
						break;
					case 'vimeo':
						var match = url.match(/(?:vimeo\.com\/)([0-9]+)/);
						id = match ? match[1] : '';
						break;
				}
				
				return id;
			},

			initYouTubeBackground: function($container, videoUrl, options) {
				var videoId = this.getVideoId(videoUrl, 'youtube');
				
				if (!videoId) return;

				var $iframe = $('<iframe></iframe>');
				var src = options.privacyMode ? 
					'https://www.youtube-nocookie.com/embed/' : 
					'https://www.youtube.com/embed/';
				
				var params = {
					autoplay: 1,
					mute: 1,
					controls: 0,
					rel: 0,
					modestbranding: 1,
					playsinline: 1,
					enablejsapi: 1
				};

				if (options.startTime) {
					params.start = options.startTime;
				}

				if (options.endTime) {
					params.end = options.endTime;
				}

				if (!options.playOnce) {
					params.loop = 1;
					params.playlist = videoId;
				}

				src += videoId + '?' + $.param(params);

				$iframe.attr({
					src: src,
					frameborder: 0,
					allow: 'autoplay; fullscreen; picture-in-picture',
					allowfullscreen: true
				});

				// Clear existing content and add iframe
				$container.empty().append($iframe);
			},

			initVimeoBackground: function($container, videoUrl, options) {
				var videoId = this.getVideoId(videoUrl, 'vimeo');
				
				if (!videoId) return;

				var $iframe = $('<iframe></iframe>');
				var src = 'https://player.vimeo.com/video/' + videoId;
				
				var params = {
					autoplay: 1,
					muted: 1,
					controls: 0,
					title: 0,
					byline: 0,
					portrait: 0,
					playsinline: 1,
					background: 1
				};

				if (options.startTime) {
					params.t = options.startTime + 's';
				}

				if (!options.playOnce) {
					params.loop = 1;
				}

				src += '?' + $.param(params);

				$iframe.attr({
					src: src,
					frameborder: 0,
					allow: 'autoplay; fullscreen; picture-in-picture',
					allowfullscreen: true
				});

				// Clear existing content and add iframe
				$container.empty().append($iframe);
			},

			initHostedVideoBackground: function($container) {
				var $video = $container.find('video');
				
				if ($video.length) {
					$video.prop({
						muted: true,
						autoplay: true
					});

					if (this.isMobile() && !$container.data('play-on-mobile')) {
						$video.prop('autoplay', false);
						return;
					}

					var playPromise = $video[0].play();
					
					if (playPromise !== undefined) {
						playPromise.catch(function(error) {
							console.log('Video autoplay failed:', error);
						});
					}
				}
			},

			onElementChange: debounce( function() {
				this.elements.$container.slick( 'unslick' );
				this.run();
				// Re-initialize lightbox and video sizing after slider is recreated
				setTimeout(() => {
					this.initVideoLightbox();
					this.initDynamicVideoSizing();
				}, 200);
			}, 200 ),

			getSlickSettings: function() {
				var settings = {
					infinite: !! this.getElementSettings( 'loop' ),
					autoplay: !! this.getElementSettings( 'autoplay' ),
					autoplaySpeed: this.getElementSettings( 'autoplay_speed' ),
					speed: this.getElementSettings( 'animation_speed' ),
					centerMode: !! this.getElementSettings( 'center' ),
					vertical: !! this.getElementSettings( 'vertical' ),
					slidesToScroll: 1,
				};

				switch ( this.getElementSettings( 'navigation' ) ) {
					case 'arrow':
						settings.arrows = true;
						break;
					case 'dots':
						settings.dots = true;
						break;
					case 'both':
						settings.arrows = true;
						settings.dots = true;
						break;
				}

				settings.slidesToShow = parseInt( this.getElementSettings( 'slides_to_show' ) ) || 1;
				settings.responsive = [
					{
						breakpoint: elementorFrontend.config.breakpoints.lg,
						settings: {
							slidesToShow: ( parseInt( this.getElementSettings( 'slides_to_show_tablet' )) || settings.slidesToShow ),
						}
					},
					{
						breakpoint: elementorFrontend.config.breakpoints.md,
						settings: {
							slidesToShow: ( parseInt( this.getElementSettings( 'slides_to_show_mobile' )) || parseInt( this.getElementSettings( 'slides_to_show_tablet' ) ) ) || settings.slidesToShow,
						}
					}
				];

				return $.extend( {}, this.getSettings(), settings );
			},

			run: function() {
				var self = this;
				this.elements.$container.slick( this.getSlickSettings() );
				
				// Ensure lightbox is initialized after slider is ready
				this.elements.$container.on('afterChange', function() {
					// Re-initialize lightbox for any new slides
					setTimeout(function() {
						self.initVideoLightbox();
					}, 50);
					
					// Re-size videos after slide change
					setTimeout(function() {
						self.resizeCurrentSlideVideo();
					}, 100);
				});
				
				// Force initial video sizing after slider is completely ready
				setTimeout(function() {
					console.log('Force initial video sizing...');
					self.resizeCurrentSlideVideo();
					
					// Additional retry for first slide
					setTimeout(function() {
						self.resizeCurrentSlideVideo();
					}, 500);
					
					// Final retry for stubborn Vimeo videos
					setTimeout(function() {
						self.resizeCurrentSlideVideo();
					}, 1500);
				}, 100);
			}
		} );

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/wpzoom-elementor-addons-video-slider.default',
			function ( $scope ) {
				elementorFrontend.elementsHandler.addHandler( SliderBase, {
					$element: $scope
				} );
			}
		);
	} );
} (jQuery));
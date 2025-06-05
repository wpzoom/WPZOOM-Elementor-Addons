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
					autoplay    : true,
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
				
				// Initialize video backgrounds and lightbox after slider is ready
				this.elements.$container.on('init', () => {
					this.initVideoBackgrounds();
					this.initVideoLightbox();
				});
			},

			initVideoBackgrounds: function() {
				var self = this;
				
				this.elements.$videoBg.each(function() {
					var $videoBg = $(this);
					var videoType = $videoBg.data('video-type');
					var videoUrl = $videoBg.data('video-url');
					var playOnMobile = $videoBg.data('play-on-mobile');

					// Check if we should play on mobile
					if (!playOnMobile && self.isMobile()) {
						$videoBg.hide();
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
					}
				});
			},

			initVideoLightbox: function() {
				// Find all lightbox triggers in this slider
				this.$element.find('.wpz-slick-lightbox-trigger').each(function() {
					var $trigger = $(this);
					var videoUrl = $trigger.attr('href');
					
					if (videoUrl) {
						$trigger.on('click', function(e) {
							e.preventDefault();
							
							// Try Elementor's lightbox first
							if (elementorFrontend && elementorFrontend.utils && elementorFrontend.utils.lightbox) {
								elementorFrontend.utils.lightbox.openSlideshow([{
									image: '',
									url: videoUrl,
									type: 'video'
								}], 0);
							} else {
								// Fallback: open in new window
								window.open(videoUrl, '_blank');
							}
						});
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

				$container.html($iframe);
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
					playsinline: 1
				};

				if (options.startTime) {
					params['#t'] = options.startTime + 's';
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

				$container.html($iframe);
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
				this.elements.$container.slick( this.getSlickSettings() );
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
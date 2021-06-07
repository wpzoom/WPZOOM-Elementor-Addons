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
					nextArrow   : $( '<div />' ).append( this.findElement( '.slick-next' ).clone().show() ).html()
				}
			},

			getDefaultElements: function () {
				return {
					$container: this.findElement( this.getSettings( 'container' ) )
				};
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
			'frontend/element_ready/wpzoom-elementor-addons-carousel.default',
			function ( $scope ) {
				elementorFrontend.elementsHandler.addHandler( SliderBase, {
					$element: $scope
				} );
			}
		);
	} );
} (jQuery));
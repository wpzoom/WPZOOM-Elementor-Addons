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

	function ownKeys( object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }
	function _objectSpread( target ) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }
	function _defineProperty( obj, key, value ) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
  
	const pluginName = 'WpzoomSetActiveOnhover';
	let defaults = {
	  classname: 'wpz-is-active',
	  triggerHandlers: ['mouseenter', 'mouseleave'],
	  triggers: '> li',
	  targets: ''
	};
  
	class Plugin {
	  constructor(element, options) {
		this._defaults = defaults;
		this._name = pluginName;
		this.options = _objectSpread(_objectSpread({}, defaults), options);
		this.DOM = {};
		this.DOM.element = element;
		this.DOM.$element = $(element);
		this.init();
	  }
  
	  init() {
		const {
		  triggers,
		  targets,
		  triggerHandlers,
		  classname
		} = this.options;
		const $triggers = this.DOM.$element.find(triggers);
		const $targets = this.DOM.$element.find(targets);
		$triggers.each((i, trigger) => {
		  const $trigger = $(trigger);
		  let $target = $targets.eq(i);
  
		  if (!$target.length) {
			$target = $trigger;
		  }
  
		  if (triggerHandlers[0] === triggerHandlers[1]) {
			$trigger.on(triggerHandlers[0], () => {
			  $target.toggleClass(classname);
			});
		  } else {
			$trigger.on(triggerHandlers[0], () => {
			  $targets.add($triggers).removeClass(classname);
			  $target.addClass(classname);
			});
  
			if (triggerHandlers[1] != null) {
			  $trigger.on(triggerHandlers[1], () => {
				$target.removeClass(classname);
			  });
			}
		  }
		});
	  }
  
	}
  
	$.fn[pluginName] = function (options) {
	  return this.each(function () {
		const pluginOptions = _objectSpread(_objectSpread({}, $(this).data('active-onhover-options')), options);
  
		if ( !$.data(this, "plugin_" + pluginName) ) {
		  $.data( this, "plugin_" + pluginName, new Plugin(this, pluginOptions) );
		}
	  });
	};
  })( jQuery );

  (function ($) {
	'use strict';

	function ownKeys( object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }
	function _objectSpread( target ) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }
	function _defineProperty( obj, key, value ) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
  
	const pluginName = 'WpzoomResponsiveBg';
	let defaults = {};
  
	class Plugin {
	  constructor(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;
		this._name = pluginName;
		this.targetImage = null;
		this.targetImage = this.element.querySelector('img');
		this.init();
	  }
  
	  init() {
		if (typeof undefined === typeof this.targetImage || null === this.targetImage) {
		  console.error('There should be an image to get the source from it.');
		  return false;
		}
  
		this.setBgImage();
		imagesLoaded(this.targetImage).on('done', this.onLoad.bind(this));
	  }
  
	  getCurrentSrc() {
		let imageSrc = this.targetImage.currentSrc ? this.targetImage.currentSrc : this.targetImage.src;
  
		if (/data:image\/svg\+xml/.test(imageSrc)) {
		  imageSrc = this.targetImage.dataset.src;
		}
  
		return imageSrc;
	  }
  
	  setBgImage() {
		this.$element.css({
		  backgroundImage: "url( ".concat(this.getCurrentSrc(), " )")
		});
	  }
  
	  onLoad() {
		this.$element.addClass('loaded');
	  }
  
	}
  
	$.fn[pluginName] = function (options) {
	  return this.each(function () {
		const pluginOptions = _objectSpread(_objectSpread({}, $(this).data('responsive-options')), options);
  
		if (!$.data(this, "plugin_" + pluginName)) {
		  $.data(this, "plugin_" + pluginName, new Plugin(this, pluginOptions));
		}
	  });
	};
  })(jQuery);
  
jQuery(document).ready(function ($) {
	$('[data-active-onhover]').WpzoomSetActiveOnhover();
	$('[data-responsive-bg=true]').WpzoomResponsiveBg();
});
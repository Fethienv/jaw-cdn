window.rehubSlider = class {
	constructor(sliderNode) {
		this.sliderNode = sliderNode;
		this.currentSlideIndex = 0;
		this.setupElements();
	}

	setupElements() {
		this.$prevArrow = jQuery(this.sliderNode).find('.rh-slider-arrow--prev');
		this.$nextArrow = jQuery(this.sliderNode).find('.rh-slider-arrow--next');
		this.$items = jQuery(this.sliderNode).find('.rh-slider-item');
		this.$thumbs = jQuery(this.sliderNode).find('.rh-slider-thumbs-item');
		this.$dots = jQuery(this.sliderNode).find('.rh-slider-dots__item');
	}

	getSlideIndex(type) {
		if (type === 'right') {
			// check if last element that scroll from start
			if (this.$items.length === this.currentSlideIndex + 1) {
				this.currentSlideIndex = -1;
			}

			this.currentSlideIndex += 1;
		} else {
			// check if current element is first and start moving from end
			if (this.currentSlideIndex === 0) {
				this.currentSlideIndex = this.$items.length;
			}

			this.currentSlideIndex -= 1;
		}

		return this.currentSlideIndex;
	}

	removeActiveClasses() {
		this.$items.each(function (i, slide) {
			jQuery(slide).removeClass('rh-slider-item--visible');
		});

		this.$thumbs.each(function (i, slide) {
			jQuery(slide).removeClass('rh-slider-thumbs-item--active');
		});

		this.$dots.each(function (i, slide) {
			jQuery(slide).removeClass('rh-slider-dots__item--active');
		});
	}

	moveSlide(index) {
		this.removeActiveClasses();
		this.$items.eq(index).addClass('rh-slider-item--visible');
		this.$thumbs.eq(index).addClass('rh-slider-thumbs-item--active');
		this.$dots.eq(index).addClass('rh-slider-dots__item--active');
	}

	addListeners() {
		const self = this;

		this.$prevArrow.on('click.bind', (ev) => {
			ev.preventDefault();
			this.moveSlide(this.getSlideIndex());
		});

		this.$nextArrow.on('click.bind', (ev) => {
			ev.preventDefault();
			this.moveSlide(this.getSlideIndex('right'));
		});

		this.$thumbs.each(function (i, item) {
			jQuery(item).on('click.bind', function (ev) {
				ev.preventDefault();
				self.currentSlideIndex = i;
				self.moveSlide(i);
			})
		});

		this.$dots.each(function (i, item) {
			jQuery(item).on('click.bind', function (ev) {
				ev.preventDefault();
				self.currentSlideIndex = i;
				self.moveSlide(i);
			});
		});
	}

	removeListeners() {
		this.$prevArrow.off('click.bind');
		this.$nextArrow.off('click.bind');
		this.$thumbs.each(function (i, item) {
			jQuery(item).off('click.bind');
		});
		this.$dots.each(function (i, item) {
			jQuery(item).off('click.bind');
		});
	}

	swipeDetect() {
		const self = this;
		let swipeDirection,
		    startX,
		    distX,
		    threshold = 100;

		const touchSurface = this.sliderNode.querySelectorAll('.rh-slider-item img');

		Array.prototype.forEach.call(touchSurface, function (element) {
			element.addEventListener('touchstart', function (e) {
				const touchObj = e.changedTouches[0];
				swipeDirection = 'none';
				startX = touchObj.pageX;
				e.preventDefault();
			}, false);

			element.addEventListener('touchmove', function (e) {
				e.preventDefault();
			}, false);

			element.addEventListener('touchend', function (e) {
				if (e.target.className.indexOf('rh-slider-arrow--prev') >= 0) {
					self.$prevArrow.trigger('click.bind');
					return;
				} else if (e.target.className.indexOf('rh-slider-arrow--next') >= 0) {
					self.$nextArrow.trigger('click.bind');
					return;

				}

				const touchObj = e.changedTouches[0];
				distX = touchObj.pageX - startX;

				if (Math.abs(distX) >= threshold) {
					swipeDirection = (distX < 0) ? 'right' : 'left';
				}

				self.moveSlide(self.getSlideIndex(swipeDirection));
				e.preventDefault();
			}, false);
		});
	}

	init(fromIndex = 0) {
		this.$items.eq(fromIndex).addClass('rh-slider-item--visible');
		this.$thumbs.eq(fromIndex).addClass('rh-slider-thumbs-item--active');
		this.$dots.eq(fromIndex).addClass('rh-slider-dots__item--active');
		this.addListeners();
		this.swipeDetect();
	}

	update() {
		this.removeActiveClasses();
		this.removeListeners();
		this.setupElements();
		this.init(this.currentSlideIndex);
	}

	destroy() {
		this.removeActiveClasses();
		this.removeListeners();
	}
};

jQuery(document).ready(function($) {
	const $sliders = jQuery('.js-hook__slider');

	if ($sliders.length === 0) {
		return false;
	}

	$sliders.each(function (i, item) {
		const slider = new window.rehubSlider(item);
		slider.init();
	});
});
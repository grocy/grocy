import Popper from "popper.js";

/* this is basically a 1 on 1 port of Bootstraps'
   DropdownMenu plug-in, but has its Elements detached.
   And pobably triggers an event or two less.

   HTML-wise it uses standard bootstrap 4 dropdown syntax,
   however the button is out of the <div class="dropdown">
   wrapper, and needs to reference the dropdown menu
   with a data-detached-element="#someSelector" attribute.

   Also this class is way less generic than Bootstraps,
   but that's okay.

   Parts of this code are taken from https://github.com/twbs/bootstrap/blob/v4-dev/js/src/dropdown.js
   which is available under the MIT License.
*/

const ESCAPE_KEYCODE = 27 // KeyboardEvent.which value for Escape (Esc) key
const SPACE_KEYCODE = 32 // KeyboardEvent.which value for space key
const TAB_KEYCODE = 9 // KeyboardEvent.which value for tab key
const ARROW_UP_KEYCODE = 38 // KeyboardEvent.which value for up arrow key
const ARROW_DOWN_KEYCODE = 40 // KeyboardEvent.which value for down arrow key
const RIGHT_MOUSE_BUTTON_WHICH = 3 // MouseEvent.which value for the right button (assuming a right-handed mouse)
const REGEXP_KEYDOWN = new RegExp(`${ARROW_UP_KEYCODE}|${ARROW_DOWN_KEYCODE}|${ESCAPE_KEYCODE}`)
const SELECTOR_VISIBLE_ITEMS = '.dropdown-item:not(.disabled):not(:disabled)'

class DetachedDropdown
{
	constructor(target, menuElement = null, scope = null)
	{
		this.scopeSelector = scope;
		if (scope != null)
		{
			this.scope = $(scope);
			var jScope = this.scope;
			this.$scope = (selector) => jScope.find(selector);
		}
		else
		{
			this.$scope = $;
			this.scope = $(document);
		}

		this.$target = this.$scope(target);
		this.target = this.$target[0];
		this.menu = menuElement != null ? this.$scope(menuElement) : this.$scope(this.$target.data('target'));
		this._popper = null;
		var self = this;

		$(document).on('keydown', (event) => self.keydownHandler(event));
		this.menu.on("click", "form", e =>
		{
			e.stopPropagation()
		})

		this.scope.on("click keyup", (event) => self.clear(event));
	}

	toggle()
	{
		if (this.menu.parent().hasClass('show'))
			this.hide();

		else
			this.show();
	}

	show()
	{
		// show always re-shows.
		this.hide()


		this._popper = new Popper(this.target, this.menu, this._getPopperConfig())

		// If this is a touch-enabled device we add extra
		// empty mouseover listeners to the body's immediate children;
		// only needed because of broken event delegation on iOS
		// https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
		if ('ontouchstart' in document.documentElement)
		{
			$(document.body).children().on('mouseover', null, $.noop)
		}

		this.menu.trigger('focus');
		this.menu.attr('aria-expanded', true)

		this.menu.toggleClass('show')
		this.menu.parent().toggleClass('show')
	}

	hide()
	{
		if (this.isDisabled() || !this.menu.parent().hasClass('show'))
			return;

		// If this is a touch-enabled device we remove the extra
		// empty mouseover listeners we added for iOS support
		if ('ontouchstart' in document.documentElement)
		{
			$(document.body).children().off('mouseover', null, $.noop)
		}

		if (this._popper)
		{
			this._popper.destroy()
		}


		this.menu.removeClass('show');
		this.menu.parent().removeClass('show');
		this.menu.attr('aria-expanded', false)
	}

	isDisabled()
	{
		return this.target.disabled || this.$target.hasClass("disabled");
	}

	_getPopperConfig()
	{
		return {
			placement: 'right',
			modifiers: {
				offset: '50px',
				flip: {
					enabled: true,
				},
				preventOverflow: {
					boundariesElement: 'viewport'
				}
			}
		}
	}

	keydownHandler(event)
	{
		if (!this.isActive() && event.target.id != this.target.id)
			return;
		// If not input/textarea:
		//  - And not a key in REGEXP_KEYDOWN => not a dropdown command
		// If input/textarea:
		//  - If space key => not a dropdown command
		//  - If key is other than escape
		//    - If key is not up or down => not a dropdown command
		//    - If trigger inside the menu => not a dropdown command
		if (/input|textarea/i.test(event.target.tagName) ?
			event.which === SPACE_KEYCODE || event.which !== ESCAPE_KEYCODE &&
			(event.which !== ARROW_DOWN_KEYCODE && event.which !== ARROW_UP_KEYCODE ||
				this.menu.length) : !REGEXP_KEYDOWN.test(event.which))
		{
			return
		}

		if (this.isDisabled())
		{
			return
		}

		if (!this.isActive() && event.which === ESCAPE_KEYCODE)
		{
			return
		}

		event.preventDefault()
		event.stopPropagation()

		if (!this.isActive() || (event.which === ESCAPE_KEYCODE || event.which === SPACE_KEYCODE))
		{
			if (event.which === ESCAPE_KEYCODE)
			{
				this.menu.trigger('focus')
			}

			this.$target.trigger('click')
			return
		}

		const items = [].slice.call(this.menu[0].querySelectorAll(SELECTOR_VISIBLE_ITEMS))
			.filter(item => $(item).is(':visible'))

		if (items.length === 0)
		{
			return
		}

		let index = items.indexOf(event.target)

		if (event.which === ARROW_UP_KEYCODE && index > 0)
		{ // Up
			index--
		}

		if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1)
		{ // Down
			index++
		}

		if (index < 0)
		{
			index = 0
		}

		items[index].focus()

	}

	isActive()
	{
		return this.menu.parent().hasClass('show');
	}

	clear(event)
	{
		if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH ||
			(event.type === 'keyup' && event.which !== TAB_KEYCODE)))
		{
			return
		}

		if (!this.menu.parent().hasClass('show'))
		{
			return
		}
		let parent = this.menu.parent()[0];

		if (event && (event.type === 'click' &&
			/input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) &&
			$.contains(parent, event.target))
		{
			return
		}

		this.hide();

	}
}
export { DetachedDropdown }
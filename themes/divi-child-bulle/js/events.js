

const on = (el, eventName, handler) => {
	if (el.addEventListener) {
		el.addEventListener(eventName, handler);
	} else {
		el.attachEvent(`on${eventName}`, () => {
			handler.call(el);
		});
	}
};

const browserReady = (handler) => {
	if (document.readyState !== 'loading') {
		handler();
	} else if (document.addEventListener) {
		document.addEventListener('DOMContentLoaded', handler);
	} else {
		document.attachEvent('onreadystatechange', () => {
			if (document.readyState !== 'loading') {
				handler();
			}
		});
	}
};


export { on, browserReady };

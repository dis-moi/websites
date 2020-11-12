
import 'jspolyfill-array.prototype.find';
import $ from 'jquery';
import Bowser from "bowser";
import delegate from 'delegate'; // query is loaded in wordpress but I will be slowly removing it as a dependency

if (!window.bull_config) {
	window.bull_config = {};
}

const LINK_UNAVAILABLE = window.bull_config.bulle_non_supporte;
const LINK_UNAVAILABLE_MOBILE = window.bull_config.bulle_non_supporte_mobile;
const LINK_POPUP_EXTENSION_CHROME = window.bull_config.bulle_lien_extension_chrome;
const LINK_POPUP_EXTENSION_FF = window.bull_config.bulle_lien_extension_firefox;
const LINK_POPUP_EXTENSION_CHROME_MOBILE = window.bull_config.bulle_lien_extension_chrome_mobile;
const LINK_POPUP_EXTENSION_FF_MOBILE = window.bull_config.bulle_lien_extension_firefox_mobile;
const LINK_OPERA = window.bull_config.bulle_lien_opera;
const LINK_EDGE = window.bull_config.bulle_lien_edge;

const el = {
	windowObjectReference: null,
	browser: null,
	isChrome: false,
	isFirefox: false,
	isDesktop: false,
	isMobile: false,
	isTablet: false,
	isEdge: false,
	isOpera: false,
	timer: null,
	dejaInstalle: false
};


const manageInstallExperienceNonDesktop = () => {
	if(!(el.isChrome || el.isFirefox)) {
		return;
	}
	const popUpURL = el.isChrome ? LINK_POPUP_EXTENSION_CHROME_MOBILE : LINK_POPUP_EXTENSION_FF_MOBILE;
	window.open(
		popUpURL,
		"Dismoi"
	);
};

/*
* Opens installation window for either FF or Chrome in desktop
* Manages the overlay too
 */
const manageInstallExperienceDesktop = () => {
	if(!(el.isChrome || el.isFirefox)) {
		return;
	}
	const popUpURL = el.isChrome ? LINK_POPUP_EXTENSION_CHROME : LINK_POPUP_EXTENSION_FF;
	el.windowObjectReference = window.open(
		popUpURL,
		"Dismoi"
	);
	el.windowObjectReference.focus();
};

/*
* Main business logic on how to handle clicks in different contexts
* If in desktop
* 	if in chrome or ff, open an install popup
* 	if edge, link to edge page
* 	if in opera, display opera page
* 	otherwise link to unavailable link
*
* If in mobile or tablet (only as good as bowser JS analysis)
* 	if in chrome or ff, open an install popup
* 	otherwise link to unavailable link
*
* if neither desktop or tablet/mobile (TV??)
* 	link to unavailable
 */
const clickInstallHandler = (e) => {
	e.preventDefault();

	if (el.isDesktop) {
		if(el.isChrome || el.isFirefox) {
			manageInstallExperienceDesktop();
		} else if (el.isEdge && LINK_EDGE ) {
			window.location.href = LINK_EDGE;
		} else if (el.isOpera && LINK_OPERA) {
			window.location.href = LINK_OPERA;
		} else {
			if (LINK_UNAVAILABLE) {
				window.location.href = LINK_UNAVAILABLE;
			}
		}
	} else if (el.isMobile || el.isTablet) { // tablet, phone
		if (el.isChrome || el.isFirefox) {
			manageInstallExperienceNonDesktop();
		} else {
			if (LINK_UNAVAILABLE_MOBILE) {
				window.location.href = LINK_UNAVAILABLE_MOBILE;
			}
		}
	} else {
		if (LINK_UNAVAILABLE) {
			window.location.href = LINK_UNAVAILABLE;
		}
	}

	return false;
};

// Add this clickInstallHandler to the window scope for access from inside react profiler app
window.clickInstallHandler = clickInstallHandler;

/*
* Checking bowser variables
 */
const setUp  = () => {

	el.browser = Bowser.getParser(window.navigator.userAgent);
	el.isChrome = el.browser.getBrowserName() === 'Chrome'; // name based for more inclusiveness
	el.isDesktop = el.browser.getPlatformType() === 'desktop';
	el.isMobile = el.browser.getPlatformType() === 'mobile';
	el.isTablet = el.browser.getPlatformType() === 'tablet';
	el.isEdge = el.browser.satisfies({edge: ">1"});
	el.isFirefox = el.browser.getBrowserName() === 'Firefox'; // name based for more inclusiveness
	el.isOpera = el.browser.satisfies({opera: ">31"}); // Opera is strange so we keep this as a satisfies call

	console.info('Browser detection: ', el);
};


/*
* Set up delegates
 */
const start = () => {

	delegate(document.body, '.bulle-installer', 'click', clickInstallHandler);
	delegate(document.body, '#restartInstallButton', 'click', clickInstallHandler);

};



const initExtensionInstaller  = () => {

	setUp();
	start();

	console.info('Scripts for installing bulle extension');

};

export { initExtensionInstaller };


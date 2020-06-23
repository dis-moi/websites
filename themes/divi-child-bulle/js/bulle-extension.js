
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
const EXTENSION_ID = window.bull_config.bulle_extension_id_chrome;
// const LINK_DEJA_INSTALLE = window.bull_config.bulle_deja_installe;
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

const closeWin = () => {
	el.windowObjectReference.close();
};

/*Function open Overlay*/
const openOverlay = () => {
	$('.overlay').delay( 80 ).fadeIn( 100 );
};

/*Functions close Overlay*/
const closeOverlay = () => {
	$('.overlay').delay( 80 ).fadeOut( 100 );
};

const manageInstallExperienceNonDesktop = () => {
	if(!(el.isChrome || el.isFirefox)) {
		return;
	}
	const popUpURL = el.isChrome ? LINK_POPUP_EXTENSION_CHROME_MOBILE : LINK_POPUP_EXTENSION_FF_MOBILE;
	let strWindowFeatures = "resizable=yes,scrollbars=yes,status=1";
	window.open(
		popUpURL,
		"Dismoi",
		strWindowFeatures
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

	const windowsHeight = $( document ).height();
	const windowsWidth = ($(window).width()/2)-20;
	const popUpURL = el.isChrome ? LINK_POPUP_EXTENSION_CHROME : LINK_POPUP_EXTENSION_FF;
	let strWindowFeatures = "width="+windowsWidth+",height="+windowsHeight+",resizable=yes,scrollbars=yes,status=1";
	if(el.isFirefox){
		let offset = 480;
		if ($(window).width() > 1200) {
			offset = 780;
		}
		const windowsLeft = window.screenX + ($(window).width()/2)-offset;
		if ($(window).width() > 1200) {
			$('body').addClass('bulles-installer-wide');
		} else {
			$('body').removeClass('bulles-installer-wide');
		}
		strWindowFeatures = "width=480,height="+windowsHeight+",left="+windowsLeft+",resizable=yes,scrollbars=yes,status=1";
	}

	el.windowObjectReference = window.open(
		popUpURL,
		"Dismoi",
		strWindowFeatures
	);
	el.windowObjectReference.focus();
	openOverlay();

	// Detect if Pop-Up close
	// Cannot use onbeforeunload because it gets called right away in chrome for link going to extension marketplace
	// in Firefox the closed property because true right away, probably because there's some forwarding happening somewhere
	// Only use the "closed" proeprty approach on non firefox browsers... not sure if there's a way to detect for something so obscure
	if (!el.isFirefox) {
		el.timer = setInterval(() => {
			if(!el.windowObjectReference || el.windowObjectReference.closed) {
				clearInterval(el.timer);
				closeOverlay();
			}
		}, 500);
	}
};

/*
* Main business logic on how to handle clicks in different contexts
* If already installed, link to already installed (not yet set up)
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
	if (el.dejaInstalle && LINK_DEJA_INSTALLE) {
		// window.location.href = LINK_DEJA_INSTALLE;
		// ToDo: set up when install check API in extension is set up
		console.info('extension already installed')
	} else {
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
	}
	return false;
};

// Add this clickInstallHandler to the window scope for access from inside react profiler app
window.clickInstallHandler = clickInstallHandler;

/*
* Not used currently. To be built out once we have an API within the extension for checking version number
 */
const testExtension = () => {
	if (!el.isChrome || !chrome) {
		return;
	}
	chrome.runtime.sendMessage(EXTENSION_ID, 'version', response => {
		if (!response) {
			console.info('No extension');
			return;
		}
		console.info('Extension version: ', response.version);
		// https://www.twilio.com/blog/2018/03/detect-chrome-extension-installed.html
		el.dejaInstalle = true;
	});
};

/*
* Checking bowser variables
 */
const setUp  = () => {

	el.browser = Bowser.getParser(window.navigator.userAgent);
	// el.isChrome = el.browser.satisfies({chrome: ">20"});
	el.isChrome = el.browser.getBrowserName() === 'Chrome'; // name based for more inclusiveness
	el.isDesktop = el.browser.getPlatformType() === 'desktop';
	el.isMobile = el.browser.getPlatformType() === 'mobile';
	el.isTablet = el.browser.getPlatformType() === 'tablet';
	el.isEdge = el.browser.satisfies({edge: ">1"});
	// el.isFirefox = el.browser.satisfies({firefox: ">=26"});
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
	delegate(document.body, '.overlay', 'click', (ev) => {
		if ( ev.target.id !== 'restartInstallButton' ){
			if  (ev.target.id !== 'h-icon') {
				closeOverlay();
			}
		}
	});
	delegate(document.body, '#notNowButton', 'click', () => {
		if (el.isFirefox) {
			closeWin();
		}
	});

	/*
	// add back in once the extension test code has been vetted properly
	if ($('.bulle-installer').length) {
		testExtension();
	}
	*/

};


const initExtensionInstaller  = () => {

	setUp();
	start();

	console.info('Scripts for installing bulle extension');

};

export { initExtensionInstaller };


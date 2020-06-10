
// import 'core-js/modules/es.array.find'; // filesize too big
import 'jspolyfill-array.prototype.find';
import $ from 'jquery';
import Bowser from "bowser";
import delegate from 'delegate'; // query is loaded in wordpress but I will be slowly removing it as a dependency

const LINK_UNAVAILABLE = window.bull_config.bulle_non_supporte;
const LINK_POPUP_EXTENSION_CHROME = window.bull_config.bulle_lien_extension_chrome;
const LINK_POPUP_EXTENSION_FF = window.bull_config.bulle_lien_extension_firefox;
const EXTENSION_ID = window.bull_config.bulle_extension_id_chrome;
const LINK_DEJA_INSTALLE = window.bull_config.bulle_deja_installe;
const LINK_OPERA = window.bull_config.bulle_lien_opera;
const LINK_EDGE = window.bull_config.bulle_lien_edge;

const el = {
	windowObjectReference: null,
	browser: null,
	isChrome: null,
	isFirefox: null,
	isEdge: null,
	isOpera: null,
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

/*Function open PopUp*/
const openRequestedPopup = (e) => {
	// fail if neither chrome or firefox
	// To Do handle mobile cases
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
		"Bulle",
		strWindowFeatures
	);
	el.windowObjectReference.focus();
	openOverlay();

	// Detect if Pop-Up close
	// Cannot use onbeforeunload because it gets called right away in chrome for link going to extension marketplace
	// in Firefox the closed property because true right away, probably because there's some forwarding happening somewhere
	// Only use the "closed" proeprty approach on non firefox browsers... not sure if there's a way to detect for some	thing so obscure
	if (!el.isFirefox) {
		el.timer = setInterval(() => {
			if(!el.windowObjectReference || el.windowObjectReference.closed) {
				clearInterval(el.timer);
				closeOverlay();
			}
		}, 500);
	}
};

const clickInstallHandler = (e) => {
	if (el.dejaInstalle && LINK_DEJA_INSTALLE) {
		window.location.href = LINK_DEJA_INSTALLE;
	} else {
		if(el.isChrome || el.isFirefox) {
			openRequestedPopup();
		} else if (el.isEdge && LINK_EDGE ) {
			window.location.href = LINK_EDGE;
		} else if (el.isOpera && LINK_OPERA) {
			window.location.href = LINK_OPERA;
		} else {
			if (LINK_UNAVAILABLE) {
				window.location.href = LINK_UNAVAILABLE;
			}
		}
	}
	e.preventDefault();
};

window.clickInstallHandler = clickInstallHandler;

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


const setUp  = () => {

	el.browser = Bowser.getParser(window.navigator.userAgent);
	el.isChrome = el.browser.satisfies({chrome: ">20"});
	el.isEdge = el.browser.satisfies({edge: ">1"});
	el.isFirefox = el.browser.satisfies({firefox: ">31"});
	el.isOpera = el.browser.satisfies({opera: ">31"});

	console.info('Browser detection: ', el);
};


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


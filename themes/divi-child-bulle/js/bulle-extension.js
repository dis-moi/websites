
import $ from 'jquery';
import Bowser from "bowser";

const LINK_OPERA = 'https://choisir.lmem.net/lopera-en-mieux/';
const LINK_UNAVAILABLE = 'https://choisir.lmem.net/le-meme-en-mieux-nest-pas-disponible-sur-votre-navigateur/';

const LINK_POPUP_EXTENSION_CHROME = "https://chrome.google.com/webstore/detail/le-m%C3%AAme-en-mieux/fpjlnlnbacohacebkadbbjebbipcknbg?hl=fr";
const LINK_POPUP_EXTENSION_FF = "https://addons.mozilla.org/fr/firefox/addon/lmem/";

const EXTENSION_ID = 'cifabmmlclhhhlhhabmbhhfocdgglljb';

const el = {
	windowObjectReference: null,
	browser: null,
	isChrome: null,
	isFirefox: null,
	isOpera: null,
	timer: null
};

const closeWin = () => {
	console.log('closeWin');
	console.log('el.windowObjectReference', el.windowObjectReference);
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
	const windowsHeight = $( document ).height();
	const windowsWidth = ($(window).width()/2)-20;
	const popUpURL = el.isChrome ? LINK_POPUP_EXTENSION_CHROME : LINK_POPUP_EXTENSION_FF;
	let strWindowFeatures = "width="+windowsWidth+",height="+windowsHeight+",resizable=yes,scrollbars=yes,status=1";
	if(el.isFirefox){
		const windowsLeft = ($(window).width()/2)-480;
		strWindowFeatures = "width=480,height="+windowsHeight+",left="+windowsLeft+",resizable=yes,scrollbars=yes,status=1";
	}

	el.windowObjectReference = window.open(
		popUpURL,
		"Bulle",
		strWindowFeatures
	);
	el.windowObjectReference.focus();
	openOverlay();

	/*Detect if Pop-Up close*/
	el.timer = setInterval(() => {
		if(el.windowObjectReference.closed) {
			clearInterval(el.timer);
			closeOverlay();
		}
	}, 500);
};

const openNewTab = (url,target) => {
	if(target==='blank'){
		window.open(url,'_blank');
	}else{
		window.location.href = url;
	}
};

const clickInstallHandler = (e) => {
	if(el.isChrome || el.isFirefox) {
		openRequestedPopup();
	} else{
		const url = el.Opera ? LINK_OPERA : LINK_UNAVAILABLE
		openNewTab(url,'blank');
	}
	e.preventDefault();
};

const testExtension = () => {
	if (!chrome) {
		return;
	}
	chrome.runtime.sendMessage(EXTENSION_ID, 'version', response => {
		if (!response) {
			console.log('No extension');
			return;
		}
		console.log('Extension version: ', response.version);
		// ToDO: handle case
		// https://www.twilio.com/blog/2018/03/detect-chrome-extension-installed.html
	});
};


const setUp  = () => {

	el.browser = Bowser.getParser(window.navigator.userAgent);
	el.isChrome = el.browser.satisfies({chrome: ">20"});
	el.isFirefox = el.browser.satisfies({firefox: ">31"});
	el.isOpera = el.browser.satisfies({opera: ">31"});

	console.info('Browser detection: ', el);
};


const start = () => {
	$('#bulle-installer').click(clickInstallHandler);

	$( ".overlay" ).click((ev) => {
		if ( ev.target.id !== 'restartInstallButton' ){
			if  (ev.target.id !== 'h-icon') {
				closeOverlay();
			}
		}
	});

	$('#notNowButton').click(() => {
		if (el.isFirefox) {
			closeWin();
		}
	});

	// testExtension();
};

const initExtensionInstaller  = () => {

	setUp();
	start();

	console.info('Scripts for installing bulle extension TEST');

};

export { initExtensionInstaller };


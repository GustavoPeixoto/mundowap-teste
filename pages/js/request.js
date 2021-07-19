import { Loading } from './loading.js'
import { Logout } from './logout.js'

const request = (url, method, params, callback, contenttype="application/json") => {
	let baseurl = window.location.href.indexOf('pages') == -1 ? "api/" : "../api/";

	let loading = new Loading();

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 401) Logout.exit();
			else {
				try { callback(JSON.parse(this.responseText), this);
				} catch(err) { console.error('Erro ao decodificar JSON\n', this.responseText, '\n', err); }
			}

			loading.close();
		}
	};

	let payload = {};
	if (method.toUpperCase() == 'GET') {
		let tojoin = [];
		let arr = Object.entries(params);
		for (let i=0; i<arr.length; i++) {
			tojoin.push(arr[i][0]+'='+arr[i][1]);
		}
		url += '?'+tojoin.join('&');
	}
	else payload = params;

	xhttp.open(method, baseurl+url, true);
	xhttp.setRequestHeader("Accept", "application/json");

	let token = localStorage.getItem("token");
	if (token) xhttp.setRequestHeader("Authorization", "Bearer "+token);

	loading.open();
	if (contenttype == "multipart/form-data")  {
		if (!(params instanceof HTMLElement) || params.tagName != "FORM") { 
			loading.close(); console.error("The request parameter must be a form element"); return; 
		}
		xhttp.send(new FormData(params));
	}
	else {
		xhttp.setRequestHeader("Content-type", contenttype);
		xhttp.send(JSON.stringify(payload));
	}
}

export { request }

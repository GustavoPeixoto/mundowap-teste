import { request } from './request.js'

const logout = () => {
	request('auth/logout', 'post', {}, (payload, response) => {
		if (response.status == 200) exit();
		else alert(payload.error);
	});
}

const exit = () => {
	if (localStorage.getItem("token")) localStorage.removeItem("token");
	let index = window.location.href.indexOf('pages');
	if (index != -1) window.location.href = window.location.href.substr(0, index);
}

const set_logout = () => {
	let btn = document.getElementById('logout');
	if (!btn) console.log('nao encontrou');
	btn.addEventListener('click', logout);
}

export { logout, exit, set_logout }
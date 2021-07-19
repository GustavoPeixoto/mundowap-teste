import { request } from './request.js'

class Logout {
	static load() {
		let btn = document.getElementById('logout');
		if (!btn) return;
		btn.addEventListener('click', this.logout);
	}

	static logout = () => {
		request('auth/logout', 'post', {}, (payload, response) => {
			if (response.status == 200) this.exit();
			else alert(payload.error);
		});
	}

	static exit = () => {
		if (localStorage.getItem("token")) localStorage.removeItem("token");
		let index = window.location.href.indexOf('pages');
		if (index != -1) window.location.href = window.location.href.substr(0, index);
	}

}

export { Logout }
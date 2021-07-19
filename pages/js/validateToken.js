import { request } 	from './request.js'
import { exit } 	from './logout.js'

const validateToken = () => {
	if (localStorage.getItem("token")) {
		request('auth/validate', 'post', {}, (payload, response) => {
			let index = window.location.href.indexOf('pages');
			if (index === -1) window.location.href = "pages";
		});
	}
	else exit();
}

export { validateToken }
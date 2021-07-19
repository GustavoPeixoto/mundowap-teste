import { request } 			from './request.js'
import { validateToken } 	from './validateToken.js'
import { Theme } 			from './theme.js'

class Login {
	constructor() {
		document.addEventListener("DOMContentLoaded", function(event) {
			this.theme = new Theme();
			validateToken();

			this.form = document.getElementById('form-login');
			this.form.addEventListener('submit', (event) => {
				event.preventDefault();

				let params = {
					username: this.form.elements.username.value,
					password: this.form.elements.password.value
				};

				request('auth/login', 'post', params, (payload, response) => {
					if (response.status == 200) {
						localStorage.setItem("token", payload.token);
						window.location.href = 'pages'
					}
					else alert(payload.error);
				});
			})
		});
	}
}

const login = new Login();


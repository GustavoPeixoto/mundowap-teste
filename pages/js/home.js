import { request } 			from './request.js'
import { validateToken } 	from './validateToken.js'
import { Theme } 			from './theme.js'
import { set_logout } 		from './logout.js'

class Home {
	constructor() {
		this.theme = new Theme();
		validateToken();
		set_logout();
	}
}

const home = new Home();
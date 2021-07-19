class Theme {
	constructor(){
		let btn = document.getElementById('theme-switch');
		if (!btn) return;
		btn.addEventListener('click', this.change);
	}

	change(event){
		if (document.body.classList.contains('default')) {
			document.body.classList.remove('default');
			document.body.classList.add('dark');
		}
		else if (document.body.classList.contains('dark')) {
			document.body.classList.remove('dark');
			document.body.classList.add('default');
		}
	}
}



export { Theme }
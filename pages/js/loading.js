class Loading {
	constructor(){
		this.loading = document.getElementById("loading");
	}

	close = () => {
		if (loading) if (loading.classList.contains('active')) loading.classList.remove('active');
	}

	open = () => {
		if (loading) if (!loading.classList.contains('active')) loading.classList.add('active');
	}
}

export { Loading }
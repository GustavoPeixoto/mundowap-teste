import { request } 			from './request.js'
import { validateToken } 	from './validateToken.js'
import { Theme } 			from './theme.js'
import { Loading } 			from './loading.js'
import { Logout } 			from './logout.js'
import { Produto } 			from './produto.js'

class Home {
	constructor() {
		this.theme 		= new Theme();
		this.loading 	= new Loading();
		// validateToken(); neste modulo nao é necessário validar o token pois ja tem uma requisicao no onload
		Logout.load();

		this.form = document.getElementById('form-sheet');
		this.form.addEventListener('submit', (event) => {
			event.preventDefault();
			request('produtos/import', 'post', this.form, this.afterImportProdutos, this.form.dataset.contenttype);
		});

		this.errors = document.getElementById('errors');

		this.produtos = document.getElementById('produtos');
		this.getProdutos();
	}

	afterImportProdutos = (payload, response) => {
		if (response.status == 200) { alert(`${payload.length} produtos cadastrados!`); this.getProdutos(); }
		else if (payload.error) alert(payload.error);
		else this.showErrors(payload);
	}

	getProdutos = () => {
		request('produtos/get', 'get', {}, (payload, response) => {
			if (response.status == 200) {
				this.showProdutos(payload);
			}
			else alert(payload.error);
		});
	}

	showProdutos = (produtos) => {
		this.loading.open();

		this.produtos.innerHTML = '';
		produtos.forEach((produto, index) => {
			let view = new Produto(produto, this);
			this.produtos.appendChild(view.htmlElement);
		});

		this.loading.close();
	}

	showErrors = (errors) => {
		if (!this.errors) return;

		errors.forEach((error, index) => {
			let li = document.createElement('li');
			let div = document.createElement('div');
			li.appendChild(div);
			let span = document.createElement('span');
			span.innerHTML = `Linha ${error.line} contém os seguintes erros: ${error.errors}!`;
			div.appendChild(span);

			let btn = document.createElement('button');
			btn.classList = "btn";
			btn.type = "button";
			btn.innerHTML = "X";
			btn.addEventListener("click", (event) => {
				let element = event.target;
				while (element.tagName != 'LI') { element=element.parentElement; }
				if (element.tagName == 'LI') element.remove();
			});
			div.appendChild(btn);
			this.errors.appendChild(li);
		});
	}

	deleteProduto = (produto) => {
		if (!window.confirm(`Deseja excluir o produto ${produto.nome}?`)) return;
		request('produtos/delete', 'DELETE', {ean: produto.ean}, this.afterDeleteProdutos);
	}

	afterDeleteProdutos = (payload, response) => {
		if (response.status == '200') alert('Excluido com sucesso!');
		else alert(payload.error);
		this.getProdutos();
	}
}

document.addEventListener("DOMContentLoaded", function(event) {
	const home = new Home();
});
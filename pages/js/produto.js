import { request } 			from './request.js'

class Produto {

	constructor(produto, component) {
		this.component = component;

		this.ean 		= produto.ean 			? produto.ean 			: null;
		this.nome 		= produto.nome 			? produto.nome 			: null;
		this.preco 		= produto.preco 		? produto.preco 		: null;
		this.estoque 	= produto.estoque 		? produto.estoque 		: null;
		this.fabricacao = produto.fabricacao 	? produto.fabricacao 	: null;

		this.htmlElement = document.createElement('div');
		this.htmlElement.classList = "row produto";

		let div = document.createElement('div');
		div.classList = "col-2";
		let element = document.createElement('p');
		element.innerHTML = this.ean ? this.ean : "-";
		div.appendChild(element);
		this.htmlElement.appendChild(div);

		div = document.createElement('div');
		div.classList = "col-3";
		element = document.createElement('p');
		element.innerHTML = this.nome ? this.nome : "-";
		div.appendChild(element);
		this.htmlElement.appendChild(div);

		div = document.createElement('div');
		div.classList = "col-2";
		element = document.createElement('p');
		if (this.preco) {
			let tmp = new Intl.NumberFormat('pt-BR', { style: 'decimal', minimumFractionDigits: 2 });
			tmp = tmp.format(this.preco);
			element.innerHTML = 'R$ ' + tmp;
		}
		else element.innerHTML = "-";
		div.appendChild(element);
		this.htmlElement.appendChild(div);

		div = document.createElement('div');
		div.classList = "col-2";
		element = document.createElement('p');
		element.innerHTML = this.estoque ? this.estoque : "-";
		div.appendChild(element);
		this.htmlElement.appendChild(div);

		div = document.createElement('div');
		div.classList = "col-2";
		element = document.createElement('p');
		if (this.fabricacao) {
			let date = new Date(this.fabricacao);
			element.innerHTML = new Intl.DateTimeFormat("pt-BR").format(date)
		}
		else element.innerHTML = "-";
		div.appendChild(element);
		this.htmlElement.appendChild(div);

		div = document.createElement('div');
		div.classList = "col-1";
		element = document.createElement('button');
		element.classList = "btn delete";
		element.addEventListener("click", this.delete);

		let icon = document.createElement('i');
		icon.classList = "fa fa-trash-alt";
		element.appendChild(icon);
		div.appendChild(element);
		this.htmlElement.appendChild(div);
	}

	delete = () => { this.component.deleteProduto(this); }
}

export { Produto }
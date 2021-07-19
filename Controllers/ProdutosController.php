<?php

namespace Controllers;

use Modules\Response;
use Modules\Util;
use Modules\TypesValidators;

use Models\Produtos;

use \PhpOffice\PhpSpreadsheet\IOFactory as PhpSheetIO;

class ProdutosController extends Controller {

	/*
		Metodo HTTP: GET
		Recebe: 
			Codigo EAN: ean (Int)
		Acao:
			Busca produtos no banco de dados
		Retorna:
			[
				{
					'ean': 			{"tipo": "int", 		"descricao": "Codigo EAN"},
					'nome': 		{"tipo": "string", 		"descricao": "Nome do produto"},
					'preco': 		{"tipo": "float", 		"descricao": "Preco do produto"},
					'estoque': 		{"tipo": "int", 		"descricao": "Quantidade em estoque do produto"},
					'fabricacao': 	{"tipo": "datetime", 	"descricao": "Data de fabricacao do produto"}
				}
			]
	*/
	public function get() {
		$where = array(
			'stmt' 		=> '',
			'values' 	=> []
		);

		if (array_key_exists('ean', $this->request)) {
			if (!TypesValidators::validateInt($this->request['ean'])) return new Response([
				'error' => 'EAN deve ser um inteiro'
			], 400);

			$where['stmt'] .= 'ean = ?';
			array_push($where['values'], $this->request['ean']);
		}

		return new Response(Util::listToArray(Produtos::get( $where['stmt'], $where['values'])), 200);
	}

	/*
		Metodo HTTP: POST
		Recebe: 
			Planilha de importacao: sheet (File)
		Acao:
			Faz a leitura da planilha e cadastra os produtos no banco de dados
		Retorna:
			[
				{"tipo": "int", "descricao": "Codigo EAN"}
			]
	*/
	public function import() {
		if (!array_key_exists("sheet", $this->files)) return new Response([
			'error' => 'Planilha (sheet) é obrigatório!'
		], 400);

		$reader 		= PhpSheetIO::createReader(PhpSheetIO::identify($this->files["sheet"]["tmp_name"]));
		$sheets 		= $reader->load($this->files["sheet"]["tmp_name"]);
		$sheet 			= $sheets->getSheet(0);
		$rows 			= $sheet->toArray();

		// removendo a primeira linha da planilha que contem os labels
		$first = array_shift($rows);

		// validando a primeira coluna de labels
		$labels = array("EAN", "NOME PRODUTO", "PREÇO", "ESTOQUE", "DATA FABRICAÇÃO");
		foreach($labels as $index=>$label) {
			if ($first[$index] != $label) return new Response([
				"error" => "Coluna ".$label." não encontrada."
			], 200);
		}

		$this->database->begin_transaction();

		$inserteds 	= array();
		$errors 	= array();
		try {
			// percorrendo as linhas da planilha
			foreach ($rows as $index => $row) {
				// quando encontrar uma linha com a primeira coluna vazia, para a leitura
				if (empty($row[0])) break;

				// setando log de erro
				$errorLog = ['line' => $index + 2, 'data' => $row];

				try {
					// instanciando produto com os valores da linha
					$produto = new Produtos([
						'ean' 			=> $row[0],
						'nome' 			=> $row[1],
						'preco' 		=> $row[2],
						'estoque' 		=> $row[3],
						'fabricacao' 	=> $row[4]
					]);

					// validando produto
					if (!isset($produto->ean)) 		throw new \Exception('EAN é obrigatório');
					if (empty($produto->ean)) 		throw new \Exception('EAN deve ser um inteiro');
					if ($produto->ean <= 0) 		throw new \Exception('EAN deve ser maior que 0');

					if (!isset($produto->nome)) 	throw new \Exception('Nome é obrigatório');

					if (!isset($produto->preco)) 	throw new \Exception('Preço é obrigatório');
					if (empty($produto->preco)) 	throw new \Exception('Preço deve ser um número');
					if ($produto->preco <= 0) 		throw new \Exception('Preço deve ser maior que 0');

					if (!isset($produto->estoque)) 	throw new \Exception('Estoque é obrigatório');
					if (empty($produto->estoque)) 	throw new \Exception('Estoque deve ser um número');
					if ($produto->estoque <= 0) 	throw new \Exception('Estoque deve ser maior que 0');

					if (!empty($row[4]) && empty($produto->fabricacao)) throw new \Exception(
						'Fabricação deve ser uma data válida'
					);

					if ($produto->exists()) throw new \Exception('Produto já foi cadastrado');

					$produto->save();

					array_push($inserteds, $produto->ean);

				} catch (\Exception $e) { 
					// se ocorrer algum erro adiciona o erro a lista de logs
					$errorLog['errors'] = $e->getMessage();
					array_push($errors, $errorLog); 
				}
			}

			// se a lista de logs nao estiver vazia executa rollback e retorna os logs
			if (!empty($errors)) { $this->database->rollback(); return new Response($errors, 400); }

			$this->database->commit();

			return new Response($inserteds, 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}

	/*
		Metodo HTTP: DELETE
		Recebe: 
			Codigo EAN: ean (Int)
		Acao:
			Busca o produto no banco de dados e faz a exclusao
		Retorna:
			[
				"message": {"tipo": "string", "descricao": "Mensagem de sucesso"},
				"produto": {
					'ean': 			{"tipo": "int", 		"descricao": "Codigo EAN"},
					'nome': 		{"tipo": "string", 		"descricao": "Nome do produto"},
					'preco': 		{"tipo": "float", 		"descricao": "Preco do produto"},
					'estoque': 		{"tipo": "int", 		"descricao": "Quantidade em estoque do produto"},
					'fabricacao': 	{"tipo": "datetime", 	"descricao": "Data de fabricacao do produto"}
				}
			]
	*/
	public function delete() {
		if (!array_key_exists('ean', $this->request)) return new Response([
			'error' => 'EAN é obrigatório'
		], 400);

		if (!TypesValidators::validateInt($this->request['ean'])) return new Response([
			'error' => 'EAN deve ser um inteiro'
		], 400);

		$produto = Produtos::get('ean = ?', [$this->request['ean']]);
		if (empty($produto)) return new Response([
			'error' => 'Produto não encontrado'
		], 404);
		$produto = $produto[0];

		$this->database->begin_transaction();
		try {
			$produto->delete();

			$this->database->commit();

			return new Response([
				'message' => 'Excluido com sucesso!',
				'produto' => $produto->toArray()
			], 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}
}

?>

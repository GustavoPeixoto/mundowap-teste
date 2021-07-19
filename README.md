<h4>mundowap-teste</h4>

<p>Repositório de avaliação do processo seletivo da Mundo Wap.</p>

<hr>

<h4>Observações</h4>
<ul>
	<li>
		Crie um arquivo chamado settings.json no diretório raiz, contendo as configurações.<br>
		Exemplo:<br>
		<pre>{
  "database": {
    "host": "localhost",
    "user": "root",
    "password": "root",
    "database": "database_name"
  },
  "jwt": {
    "hours": 24,
    "secret": "your_secret"
  }
}</pre>
	</li>
	<li>
		Após criar o banco de dados, execute o arquivo database.sql localizado no diretório raiz.
	</li>
	<li>
		O banco de dados já possui um usuário de teste cadastrado.<br>
		<pre>{
  "login": "user@mail.com",
  "senha": "1234"
}</pre>
	</li>
	<li>
		As requisitos do teste e a planilha de exemplo se encontram na pasta requirements.
	</li>
</ul>

<hr>

<h4>Especificações do Ambiente</h4>
<ul>
	<li>SO: Windows 10 x64</li>
	<li>Versão do PHP: 7.4.16</li>
	<li>Versão do Composer: 2.0.12</li>
	<li>Versão do MySQL: 8.0.23</li>
</ul>

<hr>
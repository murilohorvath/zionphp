# zionphp
Framework PHP - Um framework MVC de propósito geral, visando atender todas as demandas de qualquer tipo de sistema

## Funcionalidades

A idéia é ser simples, modificando 2 linhas do seu código já é possivel utilizar todas as funcionalidades do framework, 
as principais são:
- Plataforma para aplicações MVC com segurança e facilidade de integração
- Persistência de dados: diversos bancos como MySQL, SQLServer em outro que serão incluidos futuramente
- Gerenciamento de E-mails: e-mails, cotas, logs
- Gerenciamento de Erros: Exceções, erros de código, erros de banco
- Segurança: WAF embutido, suporte a SSL e criptografia
- Gerador de Módulos: Gere CRUD para módulos totalmente funcionais com as melhores práticas, totalmente flexivel e extensível
- Internacionalização: Use textos em seu sistema em qualquer idioma
- Bibliotecas backend: Ferramentas diversas
- Bibliotecas frontend: Ferramentas diversas

## Pré-Requisitos

- PHP >= 5.3.0 Versão que iniciou o suporte a namespace
- Apache >= 2.2 com módulo mod_rewrite instalado
- MySQL >= 5.6
- Arquivo .htaccess redirecionando todo o fluxo da aplicação para o index.php, exceto arquivos estáticos como 
imagens, estilos CSS, JavaScripts

## Como usar

1) Baixe o zip do projeto e extraia em um diretório de sua preferência. Recomendamos que fique no diretório de projetos 
junto com os projetos que utilizaram o framework.

2) Inclua o arquivo autoload.php no seu projeto 
 
```php
require(dirname(dirname(dirname(__FILE__)))."/zionphp/autoload.php");
```
 
3) Inclua uma exceção no arquivo .htaccess da raiz do seu diretório publico para que as regras de rewrite para módulos
funcione
 
```php 
RewriteCond %{REQUEST_URI} !^zion/
```
 
4) Acesse a url do seu projeto no navegador com a uri "/zion/" e siga as instruções

```php 
http://seusite.com.br/zion/
```

5) Pronto! Você já pode começar a utilizar o framework, você pode simplesmente utilizar as classes do framework (backend) 
ou utilizar também os módulos já embutidos, disponíveis com o prefixo de URI /zion/. 

## Minha IDE não reconhece as classes

Para que sua IDE "enxergue" as classes e seus métodos utilizando o recurso de auto complete, siga as instruções abaixo:
 
- Eclipse: Propriedades do Projeto > PHP > Source Paths > Build Path > Link Source.
![Eclipse](https://raw.githubusercontent.com/vcd94xt10z/zionphp/master/frontend/zion/github/eclipse.png)
 
- NetBeans: Em breve

## Melhoria continua

Este framework esta em constante atualização, portanto, pode ser que uma classe que existe hoje, não exista amanhã. 
Porém, isso só sera feito se realmente necessário para não prejudicar os utilizadores do framework

## Disclaimer

Este projeto utiliza frameworks e bibliotecas de terceiros como jquery, bootstrap, etc. 
Verifique os termos e condições das licenças individualmente e descubra se você pode utilizá-los.

Ao usar este projeto, não há nenhuma garantia ou suporte oficial
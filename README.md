# JOSÉ

**José** é un lector de RSS que creei para min (e en homenaxe a meu avó José) e que tiña que cumprir unha serie de obxectivos:

- Que funcione sobre todo en móbil, polo tanto debe ser moi sinxelo e lixeiro
- Que cargue todo o contido dunha vez, porque o vou usar sobre todo no tren, onde non hai cobertura
- Que solo cargue o contido, evitar imaxes extra, banners, etc.
- Permitir gardar entradas para recuperalas máis adiante

## Requerimentos

- Mysql
- Php 7
- Un navegador moderno (Non IE)

## Instalación

- Clonar o repositorio
- Descargar dependencias con `composer install`
- Copiar o arquivo `.env.example` para `.env` e editar os datos de conexión de base de datos e de login
- Para actualizar a base de datos, executar `composer database`
- O servidor debe apuntar ao directorio `/public`.

## Actualización

- Simplemente actualizar o git `git pull`
- Se é necesario actualizar a base de datos, executar `composer database`

## Como meter suscripcións

Podes meter automaticamente algúns rss de exemplo que estan no arquivo [subscriptions.yaml](subscriptions.yaml). Como podes ver, cada entrada ten a url do feed e, opcionalmente, o *title* (se non existe ou non queres usar o proporcionado polo feed). Tamén existe o arquivo [scrapper.yaml](scrapper.yaml) que sirve para extraer o contido das webs e así poder lelo offline. Cada entrada tería a url na que se aplica, o selector CSS do elemento que contén o contido que se quere extraer e, opcionalmente, un selector para descartar elementos dese contido (xeralmente banners, lista de posts relacionados, etc). Para meter automaticamente estes feeds na base de datos, podes executar o comando `php update.php` que le os dous arquivos *yaml* e actualiza a base de datos con ese contido. Podes meter máis elementos nese yaml e executar ese comando as veces que queiras.

Outra opción (quizáis máis fácil) é entrando na base de datos (con phpMyAdmin, SequelPro, etc) e metendo manualmente os novos rexistros.

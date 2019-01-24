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
- Crear o directorio `data` con permisos de escritura
- Para actualizar a base de datos, executar `composer database`
- O servidor debe apuntar ao directorio `/public`.

## Actualización

- Simplemente actualizar o git `git pull`
- Se é necesario actualizar a base de datos, executar `composer database`

## Como meter suscripcións

As subscripcións metense no directorio subscriptions, en formato *Yaml*. Por exemplo, podes clonar [este repositorio](https://github.com/oscarotero/my-rss-subscriptions):

```sh
git clone https://github.com/oscarotero/my-rss-subscriptions.git subscriptions
```

E logo simplemente executar o comando `composer fetch`. Podes meter novos arquivos `yml` cando queiras. O nome de cada arquivo usarase como nome da categoría de todos os feeds que conteña.

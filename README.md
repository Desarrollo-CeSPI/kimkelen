# Qué es Kimkëlen?

**Kimkëlen** es un sistema de gestión integrada de colegios secundarios realizado por el [CeSPI](http://www.cespi.unlp.edu.ar/) perteneciente a la  [Universidad Nacional de La Plata UNLP](http://www.unlp.edu.ar/).
El sistema permite personalización a través de comportamientos o _behaviors_; cada comportamiento permite definir formas de evaluación según lo resuelva el colegio, seguimiento de inasistencias, sanciones disciplinarias, impresión de boletines, generación de reportes, etcétera.

## Importante

La aplicación se ha desarrollado usando Symfony 1.2 por lo que se imponen
determinados requerimientos técnicos difíciles de cumplir con el pasar del
tiempo. Es así, como hemos desarrollado un entorno de trabajo basado en
[docker](https://www.docker.com/) para simplificar las limitaciones impuestas
por determinadas librerías que ya se encuentran obsoletas.

Utilizando docker, se simplifica tanto el desarrollo como la instalación
en producción de esta aplicación.

### Recomendaciones

Se recomienda utilizar cualquier distribución Linux y usar docker como se
explica a continuación.
En la brevedad armaremos la documentación para trabajar con windows.

# Instalación de kimkelen usando docker

## Instalación de docker

Primero es necesario que la PC del desarrollador sea alguna distribución Linux
cualquiera. Luego se debe instalar docker como se explica en la [documentación oficial](https://docs.docker.com/install/).

**Se recomienda que el usuario con el que trabaja habitualmente con el sistema
operativo sea miembro del grupo docker, para así no tener que usar el comando
`sudo docker` sino directamente `docker`**

## Instalación de docker-compose

Una vez que docker se encuentra instalado en la PC, se debe instalar
docker-compose como se indica en la [documentación oficial](https://docs.docker.com/compose/install/).

# Instalación en producción

El siguiente ejemplo, muestra como iniciar el producto considerando que ya
dispone de los pre-requisitos instalados en su sistema.

## Docker compose para producción

Cree una carpeta con el nombre del proyecto _-por ejemplo el nombre de su
colegio_ y dentro de esta carpeta cree un archivo `docker-compose.yml` con el
siguiente contenido:

```yml
version: '2'
volumes:
  db:
  disciplinary-sanction-documents:
  justification-documents:
  persons-photos:
services:
  app:
    image: registry.gitlab.com/kimkelen/kimkelen:latest
    environment:
      DB_HOST: db
      DB_NAME: kimkelen
      DB_PASSWORD: root
      DB_USERNAME: root
      DEBUG: 'false'
      FLAVOR: demo
      MEMCACHE_HOST: memcache
      MEMCACHE_PORT: '11211'
      TESTING: 'true'
      FACEBOOK_ID: NONE
      FACEBOOK_SECRET: NONE
    ports:
    - 80:80
    volumes:
    - disciplinary-sanction-documents:/app/data/disciplinary-sanction-documents
    - justification-documents:/app/data/justification-documents
    - persons-photos:/app/data/persons-photos
  memcache:
    image: memcached:1.4
    command:
    - -m
    - '256'
  db:
    image: mysql:5.6
    environment:
      MYSQL_DATABASE: kimkelen
      MYSQL_ROOT_PASSWORD: root
    volumes:
    - db:/var/lib/mysql
```

Una vez creado, correr el siguiente comando:

```
docker-compose up
```

### Notas

Debe considerar editar las variables de ambiente que permiten modificar su
instalación:

* `TESTING:` si el valor es true, muestra una leyenda que indica que es una versión de prueba
* `FLAVOR:` configura el flavor de esta instalación de kimkelen. Por defecto se
  asume **demo**.
* `DEBUG:` configura el producto para trabajar en modo dev, esto es, se muestra
  la barra de symfony y los errores con más detalle. Útil para detectar
  problemas.

## Trabajando en desarrollo

Primero es necesario clonar este repositorio:

```
git clone git@github.com:Desarrollo-CeSPI/kimkelen.git
```

> Si va a realizar cambios, se recomienda que forkee el repositorio en GitHub y
> utilice un repositorio personal para manejar sus personalizaciones bajo un
> sistema de control de versiones.

### Configuraciones basadas en variables de ambiente

La modalidad de trabajo con docker impulsa un uso de variables de ambiente para
las configuraciones de los contenedores. Es por ello, que toda la
parametrización del producto se realiza a través de variables de ambiente.

Para trabajar durante el desarrollo, se recomienda entonces usar [direnv](https://direnv.net/)
para lograr ciertas abstracciones que simplifican la labor sin pensar en que se
está trabajando usando docker.

Direnv, es un producto que al ingresar a un directorio (y cualquier
subdirectorio por debajo de un padre) que contenga un archivo `.envrc` setea las
variables de ambiente que él defina. Una vez que se sale de ese directorio, las
variables se eliminan del ambiente.

Este repositorio provee un archivo .envrc con el siguiente contenido que se usa
exclusivamente durante el proceso de desarrollo:

```bash
export COMPOSE_PROJECT_NAME=kimkelen PATH=$PWD/bin:$PATH APACHE_RUN_USER=$USER APACHE_RUN_GROUP=$(id -ng)
```

Direnv al procesar tal archivo define entonces 4 variables:

* **COMPOSE_PROJECT_NAME:** Cuando usemos docker-compose en el directorio
  `docker/` el proyecto de docker-compose se llamará kimkelen. Si esta variable
  no existiese, entonces se llamaría como el nombre del directorio, que en este
  caso es docker.
* **PATH:** altera el PATH del sistema mientras trabajamos con kimkelen.
  Esencialmente, buscará en el PATH `bin/` del directorio de este repositorio
  antes que en el path del sistema. De esta forma, la distribución podría tener
  instalado php 7, pero dentro del directorio php será 5.3. Asímismo sucede con el
  comando mysql, que accede directamente al mysql de kimkelen dentro del stack de
  docker-compose
* **APACHE_RUN_USER:** usuario con el que correrá el apache dockerizado. Se
  inicializa con su usuario del sistema.
* **APACHE_RUN_GROUP:** lo mismo para el grupo con el que corre el apache
  dockerizado.


### Iniciando el stack de trabajo

Se debe ingresar al directorio `docker/` y correr docker-compose up`:

```
cd docker/ 
docker-compose up
```

El comando anterior inicia por primera vez (o restaura de una corrida previa)
los contenedores que dan soporte a kimkelen, esto es:

* Apache con kimkelen instalado
* Memcached para maneje de sesiones
* Mysql
* PHPMyAdmin

Sólo se exportan los puertos:

* Kimkelen en el puerto 8070, se podrá acceder desde http://localhost:8070
* PHPMyAdmin en el puerto 8071, se podrá acceder desde http://localhost:8071

**Para comprobar el correcto funcionamiento del stack en desarrollo el siguiente
comando debe devolver:**

```
$ php -v
PHP 5.3.29 (cli) (built: Mar  2 2018 05:47:50) 
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2014 Zend Technologies
```
> En caso que no funcione, debe existir algún problema con direnv o el stack no
> ha sido iniciado con docker-compose

## Inicializar con datos

Una vez iniciado el stack completo, se deben correr los siguientes comandos para
inicializar el producto:

```
php symfony kimkelen:flavor demo
```

> Este comando  inicializa la visualización llamada demo. Es la personalización
> de kimkelen usada como punto de partida

```
php symfony propel:build-all-load
```

> Este comando crea la estructura y luego carga la base de datos con datos de
> prueba iniciales

```
php symfony plugin:publish
```

> Este comando actualiza la vista con los propios del flavor aplicado en el
> primer paso. Cada vez que se desee cambiar el flavor, se debe correr este
> comando

```
php symfony project:permissions
```

> Este comando pone los permisos adecuados en el filesystem para trabajar

```
php symfony cache:clear
```

> Elimina datos de cache

## Datos iniciales

El sistema se instala con algunos datos cargados a decir:

* *Usuarios:* usuarios del sistema
  * Administrador:
    * `username:` admin
    * `password:` @dm1n1strad0r
  * Preceptor:
    * `username:` preceptor
    * `password:` @pr3c3pt0r
  * Profesor:
    * `username:` profesor
    * `password:` @pr0f3s0r

* *Año lectivo:* se creará un año lectivo en estado vigente 

* *Plan de estudios:* **No se crean planes de estudio** Esto debe crearlo cada
  colegio. Queremos permitir que cada colegio *done* su plan de estudios, así lo
pueden compartir con otros colegios. 


## Problemas con la generación de PDFs

Puede que en el ambiente de desarrollo, si es que se utiliza docker, no 
funcionen los PDF, y esto se debe a que se utiliza la librería
[wkhtmltopdf](https://wkhtmltopdf.org/). El problema es que dentro del
contenedor, se utiliza el comando mencionado basándose en la URL que mantiene el
navegador. Por tanto, si se utiliza un mapeo de puertos del puerto 8000 de la
máquina local al puerto 80 del contenedor, el desarrollador va a estar
probando la aplicación utilizando http://localhost:8000, y por lo tanto, la
aplicación intentará generar un requerimiento desde dentro del contenedor a la
URL http://localhost:8000, y no podrá conectarse porque dentro del contenedor
únicamente se sirve contenido en el puerto 80.

Para solucionar este problema, se aconseja utilizar para desarrollo el puerto
80.
es que dentro del contenedor docker

## ¿Qué es el **comportamiento** o **sabor**?

Cada colegio tiene su propio esquema de enseñanza siguiendo reglas diferentes.
Kimelen provee una forma desacoplada de programar esta lógica en lo que llamamos
*sabores* o *comportamientos*

El primer comando de la lista anterior setea el *comportamiento* del colegio.
Considere que los comportamientos disponibles son los que se encuentran bajo el
directorio `flavors/`

Un ejemplo entonces, sería:

```
php symfony kimkelen:flavor demo
```


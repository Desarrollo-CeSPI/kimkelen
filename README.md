# Qué es Kimkëlen?

**Kimkëlen** es un sistema de gestión integrada de colegios secundarios realizado por el [CeSPI](http://www.cespi.unlp.edu.ar/) perteneciente a la  [Universidad Nacional de La Plata UNLP](http://www.unlp.edu.ar/).
El sistema permite personalización a través de comportamientos o _behaviors_; cada comportamiento permite definir formas de evaluación según lo resuelva el colegio, seguimiento de inasistencias, sanciones disciplinarias, impresión de boletines, generación de reportes, etcétera.

# Demo

Presentamos un sitio de demo donde se podrá probar la funcionalidad del sistema. Para acceder ingresa a [Kimkelen Demo](http://demo.kimkelen.cespi.unlp.edu.ar/) con usuado "admin" y contraseña "admin".
Los datos que se visualizan son ficticios y la base de datos se rastaurara periódicamente. 

# Instalación

La instalación de **Kimkëlen** requiere de la configuración de un Servidor de aplicaciones Web, como puede ser [Apache](http://httpd.apache.org/), configurado correctamente para ejecutar el lenguaje de programación PHP, Server así como de un servidor de Base de Datos, como MySQL.
Se recomienda la instalación del producto por parte de un usuario con conocimientos en los componenentes antes enunciados.

Para usuarios que utilizan el sistema operativo Windows se recomienda la instalación de [Wamp](http://www.wampserver.com/) o [XAMP](http://www.apachefriends.org/es/xampp.html) que incluye los componentes enumerados y permite una rápida configuración de los mismos. Tenga a bien realizar la instalación y configuración como se indica en los mencionados productos. 

Finalizada la instalación, como se indica en los pasos siguientes, el acceso al sistema deberá realizarse por medio de un navegador de internet, como Chrome, Mozilla Firefox o Internet Explorer.

## Descomprimir **Kimkëlen** 

Antes de comenzar la configuración del sistema se deberá descargar el archivo comprimido desde [Github](https://github.com/Desarrollo-CeSPI/kimkelen/) y descomprimirlo en el directorio de aplicaciones del Servidor Web seleccionado. 


## Editar la configuración de la base de datos

La configuración de la base de datos se realiza por medio de los archivos
`databases.yml` y `propel.ini`. Se proveen estos archivos a modo de ejemplo en
el directorio `config/`

```
cp config/databases.yml-default config/databases.yml
cp config/propel.ini-default config/propel.ini
```

Edite estos archivos según la configuración de su entorno. Debe espesificar el dsn (nombre de la base de datos, host, usuario de la BBDD y contraseña de la BBDD)

## Ejemplo de `databases.yml`

```yml
....
all:
  propel:
    class:        sfPropelDatabase
    param:
      classname:  PropelPDO
      dsn:        mysql:dbname=kimkelen;host=localhost
      username:   root
      password:   
      encoding:   utf8
      persistent: true
      pooling:    true
```

## Ejemplo de `propel.ini`

```yml
propel.targetPackage       = lib.model
propel.packageObjectModel  = true
propel.project             = alumnos
propel.database            = mysql
propel.database.driver     = mysql
propel.database.url        = mysql:dbname=kimkelen;host=localhost
propel.database.creole.url = ${propel.database.url}
propel.database.user       = root
propel.database.password   = 
propel.database.encoding   = utf8
...
```

*Es importante destacar que la base de datos debe crearla usted manualmente, no es
creada por ninguno de los pasos siguientes*

## Instalar por primera vez

```
php symfony kimkelen:flavor <COMPORTAMIENTO>
php symfony propel:build-all-load 
php symfony plugin:publish
php symfony project:permissions
```

En el caso de que al ejecutar el comando "php symfony propel:build-all-load" se produzca algún error, reemplazar la ejecución del misimo por la ejecución de los siguientes comandos

```
php symfony propel:build-model
php symfony propel:build-forms
php symfony propel:build-filters
php symfony propel:build-sql
php symfony propel:insert-sql
```

## Actualizar la versión

En el caso de que se desee actualizar la versión, no se deben ejecutar todos los comandos anteriores dado que algunos rearman la base de datos y se perderia información.
Entonces, cuando ya se cuenta con Kimkelen y simplemente se actualiza a una nueva versión, se deberán ejecutar solo los siguientes comandos sobre nuevamente:

```
php symfony kimkelen:flavor <COMPORTAMIENTO>
php symfony plugin:publish
php symfony project:permissions
php symfony propel:build-model
php symfony propel:build-forms
php symfony propel:build-filters
```

## Datos Iniciales

Por defecto, los comandos anteriores crean la base de datos pero no agregan los datos por defectos con lo que cuenta el sistema.
En caso de queres crearlos, ejecutar el sieguiente comando:

```
php symfony propel:data-load
```

> **Importante** Este comando borra TODOS los datos de la base. No debe ejecutarse una vez que el sistema se encuentre en uso para evitar la perdida de información 


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


# Instalación con Capistrano

Recomendamos darle una oportunidad a este excelente producto. Leer más
[aqui](https://github.com/capistrano/capistrano)

Editar el Gemfile agregando
```
source "https://rubygems.org"
gem "capifony"
```

Correr el comando `bundle update` en la raiz del proyecto

Luego capificar la aplicación:

```
cd ROOT_KIMKELEN
capifony .
```

Editar el archivo `config/deploy.rb`

## Ejemplo de archivo config/deploy.rb

```ruby
set :flavor, "demo"
set :ssh_options, { :forward_agent => true }
set :application, "kimekelen"
set :user, application
set :domain,      "desarrollo.cespi.unlp.edu.ar"
set :deploy_to,   "/opt/applications/#{application}"

set :repository,  "https://github.com/Desarrollo-CeSPI/kimkelen.git"
set :scm,         :git

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where symfony migrations will run

set :deploy_via, :remote_cache
set   :use_sudo,      false
set  :keep_releases,  3
set :symfony_version, "1.2.13"
set :use_orm, false
set :shared_files, %w(config/databases.yml config/app.yml)

 after "deploy:finalize_update" do
   symfony.propel.setup
    # Build classes
       run "#{try_sudo} #{php_bin} #{latest_release}/symfony propel:build-model"
       run "#{try_sudo} #{php_bin} #{latest_release}/symfony propel:build-forms"
       run "#{try_sudo} #{php_bin} #{latest_release}/symfony propel:build-filters"

    # Emulate
       run "#{try_sudo} #{php_bin} #{latest_release}/symfony kimkelen:flavor #{flavor} "

  symfony.cc
  symfony.plugin.publish_assets
  symfony.project.permissions
 end

```

## Comandos a correr

```
cap deploy:setup
cap deploy
```

Resta conectarse y correr el comando:

```
php symfony kimkelen:flavor <comportamiento>
```

Los comportamientos se definen en el directorio `flavors`. El comportamiento por
defecto sería *demo*

## TODO

* El `app.yml` queda vacío por lo que hay que copiarlo manualmente. Deberíamos hacer que en el primer deploy se copie

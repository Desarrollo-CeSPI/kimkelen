# Instalacion con capistrano

Editar el Gemfile agregando
```
source "https://rubygems.org"
gem "capifony"
```

Luego capificar la aplicación:

```
cd ROOT_KIMKELEN
capifony .
```

Editar el archivo `config/deploy.rb`

## Ejemplo de archivo config/deploy.rb

```ruby
set :ssh_options, { :forward_agent => true }
set :application, "kimekelen"
set :user, application
set :domain,      "desarrollo.cespi.unlp.edu.ar"
set :deploy_to,   "/opt/applications/#{application}"

set :repository,  "git@gitlab.desarrollo.cespi.unlp.edu.ar:desarrollo/kimkelen.git"
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
 end
```

## Comandos a correr

```
cap deploy:setup
cap deploy
```

Resta conectarse y correr los comandos de emulate.sh segun el sabor elegido

# TODO

* El `app.yml` queda vacío por lo que hay que copiarlo manualmente. Deberíamos hacer que en el primer deploy se copie
* Usar tags para los deploys. Hoy solo se hace del master

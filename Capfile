load 'deploy' if respond_to?(:namespace) # cap2 differentiator
Dir['plugins/*/lib/recipes/*.rb'].each { |plugin| load(plugin) }

load Gem.find_files('capifony_symfony1.rb').first.to_s
load 'config/deploy'
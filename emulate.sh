#!/bin/bash

if [ $# -ne 1 ]
then
  echo "Falta el flavor!"
  exit 1
fi

echo "Borrando assets existentes..."
rm web/images
rm web/css

if [ $1 = 'clean' ]
then
  exit
fi

echo "Copiando assets de $1..."
ln -sf ../flavors/$1/web/css web
ln -sf ../flavors/$1/web/images web
ln -sf ../../../flavors/$1/config/pdf_configs.yml apps/backend/config

echo -e "---\nnc_flavor:\n  flavors:\n    root_dir: flavors\n    current: $1" > config/nc_flavor.yml
echo -e "<?php echo ucwords('$1\n')?>" > config/school_behaviour

./symfony cc

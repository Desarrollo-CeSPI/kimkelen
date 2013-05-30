#!/bin/bash

## Alumnos demo restore script

check() {
  if [ $1 ]
  then
    echo "OK"
  else
    echo "ERROR"
  fi
}

RESTORE_SQL_DUMP=demo.sql
ALUMNOS_DEMO_DB=alumnos_demo
DB_USERNAME=root
DB_PASSWORD=dbadmtesting
ROOT_DIR=/var/www/symfony-projects/testing/alumnos_demo/restore

# Drop old db
echo -n "Dropping db $ALUMNOS_DEMO_DB..."
mysqladmin -u$DB_USERNAME -p$DB_PASSWORD -f drop $ALUMNOS_DEMO_DB
check $?

# Restore db
echo -n "Restoring db structure..."
mysqladmin -u$DB_USERNAME -p$DB_PASSWORD -f create $ALUMNOS_DEMO_DB
check $?

# Load dump into db
echo -n "Loading restore dump..."
mysql -u$DB_USERNAME -p$DB_PASSWORD $ALUMNOS_DEMO_DB < $ROOT_DIR/$RESTORE_SQL_DUMP
check $?

echo "DONE"

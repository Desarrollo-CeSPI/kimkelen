<?php

require_once(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');
require_once(dirname(__FILE__).'/../lib/dbInfo.php');
//$symfony_dir = realpath(dirname(__FILE__).'/../..');
//$configuration = new ProjectConfiguration($symfony_dir);
//sfSimpleAutoload::register();

require_once('simpletest/autorun.php');

class DbInfoTest extends UnitTestCase {

  public function testGetTableInfoFromCreate() {
    $sql = "
CREATE TABLE `album`
(
    `id` INTEGER  NOT NULL AUTO_INCREMENT,
    `id_artist` INTEGER default 0 NOT NULL,
    `name` VARCHAR(255) default '',
    `type` VARCHAR(16) default 'empty' NOT NULL,
    PRIMARY KEY (`id`),
    KEY `id_artist`(`id_artist`),
    CONSTRAINT `constraint2` FOREIGN KEY (`name`) REFERENCES `sometable` (`somename`) ON DELETE SET NULL,
    CONSTRAINT `album_FK_1`
        FOREIGN KEY (`id_artist`)
        REFERENCES `artist` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
)Type=innoDB;
      ";
    $i = new dbInfo();
    $i->getTableInfoFromCreate($sql);

    $fields = array(
      'id'        => Array('code' => 'INTEGER  NOT NULL AUTO_INCREMENT', 'type' => 'INT(11)', 'null' => 0, 'default' => null),
      'id_artist' => Array('code' => 'INTEGER default 0 NOT NULL', 'type' => 'INT(11)', 'null' => 0, 'default' => 0),
      'name'      => Array('code' => 'VARCHAR(255) default \'\'', 'type' => 'VARCHAR(255)', 'null' => 1, 'default' => ''),
      'type'      => Array('code' => 'VARCHAR(16) default \'empty\' NOT NULL', 'type' => 'VARCHAR(16)', 'null' => 0, 'default' => 'empty'),
    );
    $this->assertEqual($i->tables['album']['fields'], $fields);

    $keys = array(
      ''          => Array('type' => 'PRIMARY', 'code' => "(`id`)", 'fields' => array('`id`')),
      'id_artist' => Array('type' => '', 'code' => "(`id_artist`)", 'fields' => array('`id_artist`')),
    );
    $this->assertEqual($i->tables['album']['keys'], $keys);

    $fkeys = array(
      'album_FK_1' => Array (
         'field' => 'id_artist',
         'ref_table' => 'artist',
         'ref_field' => 'id',
         'on_delete' => 'CASCADE',
         'on_update' => 'CASCADE',
         'code' => 'CONSTRAINT `album_FK_1`
        FOREIGN KEY (`id_artist`)
        REFERENCES `artist` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE'
        ),
      'constraint2' => Array (
         'field' => 'name',
         'ref_table' => 'sometable',
         'ref_field' => 'somename',
         'on_delete' => 'SET NULL',
         'on_update' => 'RESTRICT',
         'code' => 'CONSTRAINT `constraint2` FOREIGN KEY (`name`) REFERENCES `sometable` (`somename`) ON DELETE SET NULL'
        )
    );
    $this->assertEqual($i->tables['album']['fkeys'], $fkeys);

    $this->assertEqual($i->tables['album']['type'], 'innodb');
  }

  public function testDiff() {
    $sql = "
CREATE TABLE `album`
(
    `id` INTEGER  NOT NULL AUTO_INCREMENT,
    `id_artist` INTEGER default 0 NOT NULL,
    `name` VARCHAR(255) default '',
    `type` VARCHAR(16) default 'empty' NOT NULL,
    PRIMARY KEY (`id`),
    KEY `id_artist`(`id_artist`),
    CONSTRAINT `album_FK_1`
        FOREIGN KEY (`id_artist`)
        REFERENCES `artist` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
)Type=innoDB;
      ";
    $i = new dbInfo();
    $i->getTableInfoFromCreate($sql);
    $this->assertEqual($i->getDiffWith($i), '');
  }

}


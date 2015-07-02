<?php

class dbInfo {
  public $tables = array();
  public $debug = false;

  function loadFromDb($con) {
    $stmt = $con->prepare("SHOW FULL TABLES");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_NUM);

    if($stmt->rowCount()==0) return false;
    while($row = $stmt->fetch()) {
        if(strtoupper($row[1])=="BASE TABLE") {
            $this->tables[$row[0]] = array();
        }
    };

    foreach($this->tables as $table => $null) {
      $stmt = $con->prepare("show create table `".$table."`");
      $stmt->execute();
      $row = $stmt->fetch();
      $create_table = $row[1];
      $this->getTableInfoFromCreate($create_table);
    }

    return true;
  }

  public function loadFromFile($filename) {
    $dump = file_get_contents($filename);
    preg_match_all('/create table ([^\'";]+|\'[^\']*\'|"[^"]*")+;/i', $dump, $matches);
    foreach($matches[0] as $key=>$value) {
      $this->getTableInfoFromCreate($value);
    }
  }

  public function loadAllFilesInDir($dir) {
    $files = sfFinder::type('file')->name('*.schema.sql')->follow_link()->in($dir);
    foreach($files as $file) $this->loadFromFile($file);
  }

  public function getTableInfoFromCreate($create_table) {
    preg_match("/^\s*create table `?([^\s`]+)`?\s+\((.*)\)([^\)]*)$/mis", $create_table, $matches);
    $table = $matches[1];
    $code = $matches[2];
    $table_info = $matches[3];

    $this->tables[$table]['create'] = $create_table;
    $this->tables[$table]['fields'] = array();
    $this->tables[$table]['keys'] = array();
    $this->tables[$table]['fkeys'] = array();

    if(preg_match('/(type|engine)=(\w+)/i', $table_info, $matches)) {
        $this->tables[$table]['type'] = strtolower($matches[2]);
    } else {
        $this->tables[$table]['type'] = '';
    }

    preg_match_all('/\s*(([^,\'"\(]+|\'[^\']*\'|"[^"]*"|\(([^\(\)]|\([^\(\)]*\))*\))+)\s*(,|$)/', $code, $matches);
    foreach($matches[1] as $key=>$value) {
      $this->getInfoFromPart($table, trim($value));
    }
  }

  public function getInfoFromPart($table, $part) {
    //get fields codes
    if(preg_match("/^`(\w+)`\s+(.*)$/m", $part, $matches)) {
      $fieldname = $matches[1];
      $code = $matches[2];
      $this->tables[$table]['fields'][$fieldname]['code'] = $code;
      $res = preg_match('/([^\s]+)\s*(NOT NULL)?\s*(default (\'([^\']*)\'|(-?\d+)))?\s*(NOT NULL)?/i', $code, $matches2);
      $type = strtoupper($matches2[1]);
      if($type=='TINYINT') $type = 'TINYINT(4)';
      if($type=='SMALLINT') $type = 'SMALLINT(6)';
      if($type=='INTEGER') $type = 'INT(11)';
      if($type=='BIGINT') $type = 'BIGINT(20)';
      if($type=='BLOB') $type = 'TEXT';   //propel fix, blob is TEXT field with BINARY collation
      $type = str_replace('VARBINARY', 'VARCHAR', $type);
      $type = str_replace('INTEGER', 'INT', $type);
      $this->tables[$table]['fields'][$fieldname] = array(
        'code'    => $code,
        'type'    => $type,
      );
      // null value
      $this->tables[$table]['fields'][$fieldname]['null'] = true;
      if (isset($matches2[2]) and $matches2[2] == "NOT NULL")
      {
        $this->tables[$table]['fields'][$fieldname]['null'] = false;
      }
      if (isset($matches2[7]) and $matches2[7] == "NOT NULL")
      {
        $this->tables[$table]['fields'][$fieldname]['null'] = false;
      }

      // default value
      $this->tables[$table]['fields'][$fieldname]['default'] = "";
      if (isset($matches2[6]) and $matches2[6] != "")
      {
        $this->tables[$table]['fields'][$fieldname]['default'] = $matches2[6];
      }
      elseif (isset($matches2[5]))
      {
        $this->tables[$table]['fields'][$fieldname]['default'] = $matches2[5];
      }
    }

    //get key codes
    elseif(preg_match("/^(primary|unique|fulltext)?\s*(key|index)\s+(`(\w+)`\s*)?(.*?)$/mi", $part, $matches)) {
      $keyname = $matches[4];
      $this->tables[$table]['keys'][$keyname]['type'] = $matches[1];
      $this->tables[$table]['keys'][$keyname]['code'] = $matches[5];
      $this->tables[$table]['keys'][$keyname]['fields'] = preg_split('/,\s*/', substr($matches[5], 1, -1));
    }

    elseif(preg_match("/CONSTRAINT\s+\`(.+)\`\s+FOREIGN KEY\s+\(\`(.+)\`\)\s+REFERENCES \`(.+)\` \(\`(.+)\`\)/mi", $part, $matches)) {
      $name = $matches[1];
      $this->tables[$table]['fkeys'][$name] = array(
                        'field' => $matches[2],
                        'ref_table' => $matches[3],
                        'ref_field' => $matches[4],
                        'code' => $part,
      );
      if(preg_match('/ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION)/i', $part, $matches)) {
        $this->tables[$table]['fkeys'][$name]['on_delete'] = strtoupper($matches[1]);
      } else {
        $this->tables[$table]['fkeys'][$name]['on_delete'] = 'RESTRICT';
      }
      if(preg_match('/ON UPDATE (RESTRICT|CASCADE|NO ACTION)/i', $part, $matches)) {
        $this->tables[$table]['fkeys'][$name]['on_update'] = strtoupper($matches[1]);
      } else {
        $this->tables[$table]['fkeys'][$name]['on_update'] = 'RESTRICT';
      }
    }

    else {
      throw new Exception("can't parse line '$part' in table $table");
    }
  }

  function tableSupportsFkeys($tabletype) {
      return !in_array($tabletype, array('myisam', 'ndbcluster'));
  }


  private function getTableTypeDiff($db_info2) {
    $diff_sql = "";
    foreach($db_info2->tables as $tablename=>$tabledata) {
      if(empty($this->tables[$tablename])) continue;
      //change table type
      $old_table_type = $this->tables[$tablename]['type'];
      if($this->tables[$tablename] && $tabledata['type']!=$old_table_type) {
        $diff_sql .= "ALTER TABLE `$tablename` engine={$tabledata['type']};\n";
      }
    }
    return $diff_sql;
  }


  function getDiffWith(dbInfo $db_info2) {

    $diff_sql = '';

    $diff_sql .= $this->getTableTypeDiff($db_info2);

    $table_sql = array();

    //adding columns, indexes, etc
    foreach($db_info2->tables as $tablename=>$tabledata) {

      //check for new table
      if(!isset($this->tables[$tablename])) {
        $diff_sql .= "\n".$db_info2->tables[$tablename]['create']."\n";
        continue;
      }

      //check for new field
      foreach($tabledata['fields'] as $field=>$fielddata) {
        $mycode = $fielddata['code'];
        $othercode = @$this->tables[$tablename]['fields'][$field]['code'];
        if($mycode and !$othercode) {
          $table_sql[$tablename][] = "ADD `$field` $mycode";
        };
      };

      //check for new index
      if($tabledata['keys']) foreach($tabledata['keys'] as $field=>$fielddata) {
        $mycode = $fielddata['code'];
        $otherdata = @$this->tables[$tablename]['keys'][$field];
        $othercode = @$otherdata['code'];
        if($mycode and !$othercode) {
          if($fielddata['type']=='PRIMARY') {
            $table_sql[$tablename][] = "ADD PRIMARY KEY $mycode";
          } else {
            $table_sql[$tablename][] = "ADD {$fielddata['type']} INDEX `$field` $mycode";
          }
        };
      };

      //check for new foreign key
      if($tabledata['fkeys'] && $this->tableSupportsFkeys($tabledata['type'])) {
        foreach($tabledata['fkeys'] as $fkeyname=>$data) {
          $mycode = $data['code'];
          $othercode = @$this->tables[$tablename]['fkeys'][$fkeyname]['code'];
          if($mycode && !$othercode) {
            $table_sql[$tablename][] = "ADD {$mycode}";
          };
        }
      };
    };

    //modifying and deleting columns, indexes, etc
    foreach($this->tables as $tablename=>$tabledata) {

      //check table exists
      if(!isset($db_info2->tables[$tablename])) {
        $diff_sql .= "DROP TABLE `$tablename`;\n";
        continue;
      }

      //drop, alter foreign key
      if($tabledata['fkeys'] && $this->tableSupportsFkeys($tabledata['type'])) {
        foreach($tabledata['fkeys'] as $fkeyname=>$data) {
          $mycode = $data['code'];
          $othercode = @$db_info2->tables[$tablename]['fkeys'][$fkeyname]['code'];
          if($mycode and !$othercode) {
            $diff_sql .= "ALTER TABLE `$tablename` DROP FOREIGN KEY `$fkeyname`;\n";
          } else {
            $data2 = $db_info2->tables[$tablename]['fkeys'][$fkeyname];
            if ($data['ref_table'] != $data2['ref_table'] ||
            $data['ref_field'] != $data2['ref_field'] ||
            $data['on_delete'] != $data2['on_delete'] ||
            $data['on_update'] != $data2['on_update']) {
              if($this->debug) {
                $diff_sql .= "/* old definition: $mycode\n   new definition: $othercode */\n";
              }
              $diff_sql .= "ALTER TABLE `$tablename` DROP FOREIGN KEY `$fkeyname`;\n";
              $table_sql[$tablename][] = "ADD {$othercode}";
            }
          };
        };
      }

      //drop, alter index
      if($tabledata['keys']) foreach($tabledata['keys'] as $field=>$fielddata) {
        $otherdata = @$db_info2->tables[$tablename]['keys'][$field];
        $ind_name = @$otherdata['type']=='PRIMARY'?'PRIMARY KEY':"{$otherdata['type']} INDEX";
        if($fielddata['code'] and !$otherdata['code']) {
          if($fielddata['type']=='PRIMARY') {
            $table_sql[$tablename][] = "DROP PRIMARY KEY";
          } else {
            $table_sql[$tablename][] = "DROP INDEX $field";
          }
        } elseif($fielddata['fields'] != $otherdata['fields'] or $fielddata['type']!=$otherdata['type']) {
          if($this->debug) {
            $diff_sql .= "/* old definition: {$fielddata['code']}\n   new definition: {$otherdata['code']} */\n";
          }
          if($fielddata['type']=='PRIMARY') {
            $table_sql[$tablename][] = "DROP PRIMARY KEY";
          } else {
            $table_sql[$tablename][] = "DROP INDEX $field";
          }
          $table_sql[$tablename][] = "ADD $ind_name ".($field?"`$field`":"")." {$otherdata['code']}";
        };
      };

      //drop, alter field
      foreach($tabledata['fields'] as $field=>$fielddata) {
        $mycode = $fielddata['code'];
        $otherdata = @$db_info2->tables[$tablename]['fields'][$field];
        $othercode = @$otherdata['code'];
        if($mycode and !$othercode) {
          $table_sql[$tablename][] = "DROP `$field`";
        } elseif($fielddata['type'] != $otherdata['type']
        or $fielddata['null'] != $otherdata['null']
        or $fielddata['default'] != $otherdata['default']   ) {
          if($this->debug) {
            $diff_sql .= "/* old definition: $mycode\n   new definition: $othercode */\n";
          }
          $table_sql[$tablename][] = "CHANGE `$field` `$field` $othercode";
        };
      };
    };

    foreach($table_sql as $table=>$statements) {
    	$diff_sql .= "ALTER TABLE `$table` ".join(', ', $statements).";\n";
    }

    if($diff_sql) $diff_sql = "SET FOREIGN_KEY_CHECKS=0;\n$diff_sql";
    return $diff_sql;
  }

  public function executeSql($sql, $connection) {
      $queries = $this->explodeSql($sql);
      foreach($queries as $query) {
        $this->executeQuery($query, $connection);
      }
  }

  public function explodeSql($sql) {
    $result = array();
    preg_match_all('/([^\'";]+|\'[^\']*\'|"[^"]*")+;/i', $sql, $matches);
    foreach($matches[0] as $query) {
      $result[] = $query;
    }
    return $result;
  }

  public function executeQuery($query, $connection) {
    $stmt = $connection->prepare($query);
    $stmt->execute();
    return $stmt;
  }


};
?>
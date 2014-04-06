<?php

class HitFixture extends CakeTestFixture {

  /* Optional. Set this property to load fixtures to a different test datasource */
  public $useDbConfig = 'test';

  public $fields = array(
    'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
    'model' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => null),
    'user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
    'indexes' => array(
      'PRIMARY' => array('column' => 'id', 'unique' => 1)
      ),
    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );
}

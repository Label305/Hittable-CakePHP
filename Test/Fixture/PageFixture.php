<?php

class PageFixture extends CakeTestFixture {

      /* Optional. Set this property to load fixtures to a different test datasource */
      public $useDbConfig = 'test';

      public $fields = array(
          'id' => array('type' => 'integer', 'key' => 'primary'),
          'title' => array('type' => 'string', 'length' => 255, 'null' => false),
          'created' => 'datetime',
          'modified' => 'datetime'
      );
      public $records = array(
          array(
            'id' => 1,
            'title' => 'About', 
            'created' => '2014-03-18 10:39:23',
            'modified' => '2014-03-18 10:41:31'
            ),
          array(
            'id' => 2,
            'title' => 'Contact', 
            'created' => '2014-03-18 10:39:23',
            'modified' => '2014-03-18 10:41:31'
            )
      );
 }

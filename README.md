# CakePHP Hittable Behavior [![Build Status](https://travis-ci.org/Label305/CakePHP-Hittable.svg?branch=master)](https://travis-ci.org/Label305/CakePHP-Hittable)

For registering hits on models

## Install

Add the repository to your requirements and load using composer

```php
    "require": {
        "label305/hittable": "dev-master"
    }
```

To install the corresponding `hits` table, run `schema create --plugin=Hittable` in the Cake shell

## Usage

### Basic

Load `Hittable.Hittable` as behavior in the model you want to have hits registered for. After this you can register hits like:

```php
	$this->Page->id = 1;
	$this->Page->registerHit();
```

Which will result in an entry in the hits table.

To retrieve the hits you can use the `hits` call like you've been used to with `find`. For example:

```php
	$this->Page->hits('count', array(
		'conditions' => array(
			'Page.id' => 1
		)
	));
```

will return the number of hits on Page with id 1, note that this call is internally handled by calling find on the Hit model which has a belongsTo association that handle the conditions.

### Automatic

If you want to set and forget it is possible to turn on automatic registering of hits. You can turn this on by settings the `automatic` settings while loading:

```php
	$actsAs = array(
		'Hittable.Hittable' => array(
			'automatic' => true
		)
	);
``` 

## License

Copyright 2014 Label305 B.V.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
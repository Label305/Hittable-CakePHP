<?php
App::uses('AuthComponent', 'Controller/Component');
/**
 * The Hittable behavior allows for registering hits on a model
 * 
 * @author Joris Blaak <joris@label305.com>
 * @requires CakePHP 2.x
 *
 * Copyright (c) 2014 Label305. All Rights Reserved.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 */
class HittableBehavior extends ModelBehavior {

	/**
	 * Settings of current hittable:
	 *
	 * - referenceTime, time used as "now" value, mainly for testing purposes
	 * 
	 * @var array
	 */
	public $settings = array(
		'referenceTime' => null,
		'automatic' => false
		);

	/**
	 * Model to store hits in
	 * @var Model
	 */
	public $Hit = null;

	/**
	 * Setup of the hittable behavior
	 * @param  Model  $model    
	 * @param  array  $settings 
	 * @return void
	 */
	public function setup(Model $model, $settings = array()) {
		$this->settings['referenceTime'] = date('Y-m-d H:i:s');

		$this->settings = Hash::merge($this->settings, $settings);

		//Set the model
		$this->Hit = ClassRegistry::init('Hittable.Hit');
	}
		
	/**
	 * Register a simple hit
	 * @param  Model $model
	 * @param  integer $id  (optional)
	 * @return array|boolean
	 */
	public function registerHit(Model $model, $id = null) {
		if(is_null($id)) {
			$id = $model->id;
		}

		$this->Hit->create();
		return $this->Hit->save(array(
			'model' => $model->alias,
			'foreign_key' => $id,
			'user_id' => AuthComponent::User('id')
			));
	}

	/**
	 * Find hits as and alias of find method, conditions will be extended
	 * with hit specific data
	 * @param  Model $model
	 * @param  string $type    (optional) type of find: count, all or first
	 * @param  array  $options (optional) find options, merged and directly passed to find
	 * @return mixed
	 */
	public function hits(Model $model, $type = 'count', $options = array()) {
		$this->_bindModel($model);

		$defaults = array(
			'recursive' => 0,
			'conditions' => array(
				$this->Hit->alias.'.model' => $model->alias
				)
			);
		$options = Hash::merge($defaults, $options);

		return $this->Hit->find($type, $options);
	}

	/**
	 * Automatic regsitering of hits
	 * @param  array  $results 
	 * @param  boolean $primary 
	 * @return void
	 */
	public function afterFind(Model $model, $results, $primary = false) {
		if($this->settings['automatic'] && $primary) {
			foreach($results as $result) {
				if(!empty($result[$model->alias][$model->primaryKey])) {
					$this->registerHit($model, $result[$model->alias][$model->primaryKey]);
				}
			}
		}
	}

	/**
	 * Setup association between Hit model and current model
	 * @param  Model  $model 
	 * @return void
	 */
	private function _bindModel(Model $model) {
		$this->Hit->bindModel(array(
			'belongsTo' => array(
				$model->alias => array(
					'foreignKey' => 'foreign_key',
					'conditions' => array(
						$this->Hit->alias.'.model' => $model->alias
						)
					)
				)
			));
	}

}
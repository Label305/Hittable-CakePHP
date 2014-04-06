<?php
App::uses('AuthComponent', 'Controller/Component');
/**
 * The Hittable behavior allows for registering hits on a model
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
		'referenceTime' => null
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
	 * @return array|boolean
	 */
	public function registerHit(Model $model) {
		$this->Hit->create();
		return $this->Hit->save(array(
			'model' => $model->alias,
			'foreign_key' => $model->id,
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
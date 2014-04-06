<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');

// A mock controller to test against
class TestHittableController extends Controller {
    
    public $uses = array();

    public $components = array(
        'Auth' => array(
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'User'
                    )
                )
            )
        );
}

class HittableTest extends CakeTestCase {

    public $fixtures = array(
    	'plugin.Hittable.page',
    	'plugin.Hittable.hit'
    	);

	/**
	 * Helper function to initialize controllers, will set $this->Controller
	 * @param  String $controller
	 * @return void
	 */
	public function initController($controllerName) {

    Configure::write('Security.salt', '8sdgwhi56lhs3kh23a4r2lafhmjhnf54kuhm4w5c');
    Configure::write('Security.cipherSeed', '922107460123958703413098562346');

    $CakeRequest = new CakeRequest();
    $CakeResponse = new CakeResponse();
    $this->Controller = new $controllerName($CakeRequest, $CakeResponse);

    $this->Controller->constructClasses();
    $this->Controller->startupProcess();
	}

    /**
     * Setup with example model
     */
    public function setUp() {
        parent::setUp();
        $this->Page = ClassRegistry::init('Page');
        $this->Hit = ClassRegistry::init('Hittable.Hit');

        $this->initController('TestHittableController');
    	$this->Controller->Auth->logout();

    	$this->bindBehavior();
    }

    /**
     * Ease up binding of the model
     * @param  array  $options options
     * @return void
     */
    public function bindBehavior($options = array()) {
    	$this->Page->Behaviors->unload('Hittable.Hittable');
    	$this->Page->Behaviors->load('Hittable.Hittable', $options);
    }

    /**
     * Generic hit, with or without a foreign key set
     * @return void
     */
    public function testHit() {
    	$this->setUp();

    	$result = $this->Page->registerHit();
    	$this->assertEquals($result['Hit']['model'], 'Page', 'Page model not set for hit');
    	$this->assertEquals($result['Hit']['foreign_key'], false, 'Foreign key for generic hit not false');

    	$this->Page->id = 1;
    	$result = $this->Page->registerHit();
    	$this->assertEquals($result['Hit']['model'], 'Page', 'Page model not set for hit');
    	$this->assertEquals($result['Hit']['foreign_key'], 1, 'Foreign key for set id not registered');

    }

    /**
     * Authenticated hit, check if user_id is passed to hit
     * @return void
     */
  	public function testAuthenticatedHit() {
    	$this->setUp();

    	//Not logged in
    	$result = $this->Page->registerHit();
    	$this->assertEquals($result['Hit']['user_id'], false, 'Not logged in user has id passed to hit');

    	//Logged in
    	$this->Controller->Auth->login(array(
    		'id' => 30
    		));	
    	$result = $this->Page->registerHit();
    	$this->assertEquals($result['Hit']['user_id'], 30, 'Logged in user not passed to hit');

    	//Logged out
    	$this->Controller->Auth->logout();
    	$result = $this->Page->registerHit();
    	$this->assertEquals($result['Hit']['user_id'], false, 'Not logged in user has id passed to hit');
  	}

  	/**
  	 * Test fetching of hits
  	 * @return void
  	 */
  	public function testHits() {
  		$this->setUp();

  		//Two empty ones
    	$result = $this->Page->registerHit();
    	$result = $this->Page->registerHit();

      //Regsiter hit with specific page
      $result = $this->Page->registerHit(1);

    	//Three with specific page
  		$this->Page->id = 1;
    	$result = $this->Page->registerHit();
      $result = $this->Page->registerHit();

      //Register hit for other page
    	$result = $this->Page->registerHit(2);
    	
    	//Find count of all
    	$result = $this->Page->hits();
    	$this->assertEquals($result, 6, 'Invalid hit count');
    	
    	//Find count of page 1
    	$result = $this->Page->hits('count', array(
    		'conditions' => array(
    			'Page.id' => 1
    			)
    		));
    	$this->assertEquals($result, 3, 'Invalid hit count');
  	}

    /**
     * Automatic hitting will register after each find query
     * @return void
     */
    public function testAutomaticHit() {
      $this->setUp();
      $this->bindBehavior(array('automatic' => true));

      //Will register hits on all
      $this->Page->find('all');

      //Will register hit on only Page with id 1
      $this->Page->find('first');

      //Find count of page 1
      $result = $this->Page->hits('count', array(
        'conditions' => array(
          'Page.id' => 1
          )
        ));
      $this->assertEquals($result, 2, 'Invalid hit count');

      //Find count of page 2
      $result = $this->Page->hits('count', array(
        'conditions' => array(
          'Page.id' => 2
          )
        ));
      $this->assertEquals($result, 1, 'Invalid hit count');

    }

}
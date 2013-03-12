<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Input\Input;
use Joomla\Input\Cookie;
use Joomla\Test\Helper;

/**
 * Test class for Input.
 *
 * @since  1.0
 */
class JInputTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The test class.
	 *
	 * @var  Input
	 */
	protected $class;

	/**
	 * Test the Input::__construct method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__construct()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Test the Input::__get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__call()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Test the Input::__get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__get()
	{
		$_POST['foo'] = 'bar';

		// Test the get method.
		$this->assertThat(
			$this->class->post->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// Test the set method.
		$this->class->post->set('foo', 'notbar');
		$this->assertThat(
			$_POST['foo'],
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		$this->markTestIncomplete();
	}

	/**
	 * Test the Input::count method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCount()
	{
		$this->assertEquals(
			count($_REQUEST),
			count($this->class)
		);

		$this->assertEquals(
			count($_POST),
			count($this->class->post)
		);

		$this->assertEquals(
			count($_GET),
			count($this->class->get)
		);
	}

	/**
	 * Test the Input::get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGet()
	{
		$_REQUEST['foo'] = 'bar';

		// Test the get method.
		$this->assertThat(
			$this->class->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		$_GET['foo'] = 'bar2';

		// Test the get method.
		$this->assertThat(
			$this->class->get->get('foo'),
			$this->equalTo('bar2'),
			'Line: ' . __LINE__ . '.'
		);

		// Test the get method.
		$this->assertThat(
			$this->class->get('default_value', 'default'),
			$this->equalTo('default'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::def method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDef()
	{
		$_REQUEST['foo'] = 'bar';

		$this->class->def('foo', 'nope');

		$this->assertThat(
			$_REQUEST['foo'],
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		$this->class->def('Joomla', 'is great');

		$this->assertThat(
			$_REQUEST['Joomla'],
			$this->equalTo('is great'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::set method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSet()
	{
		$_REQUEST['foo'] = 'bar2';
		$this->class->set('foo', 'bar');

		$this->assertThat(
			$_REQUEST['foo'],
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetArray()
	{
		$filterMock = new JFilterInputMockTracker;

		$array = array(
			'var1' => 'value1',
			'var2' => 34,
			'var3' => array('test')
		);
		$input = new Input(
			$array,
			array('filter' => $filterMock)
		);

		$this->assertThat(
			$input->getArray(
				array('var1' => 'filter1', 'var2' => 'filter2', 'var3' => 'filter3')
			),
			$this->equalTo(array('var1' => 'value1', 'var2' => 34, 'var3' => array('test'))),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$filterMock->calls['clean'][0],
			$this->equalTo(array('value1', 'filter1')),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$filterMock->calls['clean'][1],
			$this->equalTo(array(34, 'filter2')),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$filterMock->calls['clean'][2],
			$this->equalTo(array(array('test'), 'filter3')),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::get method using a nested data set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetArrayNested()
	{
		$filterMock = new JFilterInputMockTracker;

		$array = array(
			'var2' => 34,
			'var3' => array('var2' => 'test'),
			'var4' => array('var1' => array('var2' => 'test'))
		);
		$input = new Input(
			$array,
			array('filter' => $filterMock)
		);

		$this->assertThat(
			$input->getArray(
				array('var2' => 'filter2', 'var3' => array('var2' => 'filter3'))
			),
			$this->equalTo(array('var2' => 34, 'var3' => array('var2' => 'test'))),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$input->getArray(
				array('var4' => array('var1' => array('var2' => 'filter1')))
			),
			$this->equalTo(array('var4' => array('var1' => array('var2' => 'test')))),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$filterMock->calls['clean'][0],
			$this->equalTo(array(34, 'filter2')),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$filterMock->calls['clean'][1],
			$this->equalTo(array(array('var2' => 'test'), 'array')),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::getArray method without specified variables.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetArrayWithoutSpecifiedVariables()
	{
		$array = array(
			'var2' => 34,
			'var3' => array('var2' => 'test'),
			'var4' => array('var1' => array('var2' => 'test')),
			'var5' => array('foo' => array()),
			'var6' => array('bar' => null),
			'var7' => null
		);

		$input = new Input($array);

		$this->assertEquals($input->getArray(), $array);
	}

	/**
	 * Test the Input::get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetFromCookie()
	{
		// Check the object type.
		$this->assertThat(
			$this->class->cookie instanceof Cookie,
			$this->isTrue(),
			'Line: ' . __LINE__ . '.'
		);

		$_COOKIE['foo'] = 'bar';

		// Test the get method.
		$this->assertThat(
			$this->class->cookie->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Input::getMethod method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetMethod()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Test the Input::serialize method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSerialize()
	{
		// Load the inputs so that the static $loaded is set to true.
		Helper::invoke($this->class, 'loadAllInputs');

		// Adjust the values so they are easier to handle.
		Helper::setValue($this->class, 'inputs', array('server' => 'remove', 'env' => 'remove', 'request' => 'keep'));
		Helper::setValue($this->class, 'options', 'options');
		Helper::setValue($this->class, 'data', 'data');

		$this->assertThat(
			$this->class->serialize(),
			$this->equalTo('a:3:{i:0;s:7:"options";i:1;s:4:"data";i:2;a:1:{s:7:"request";s:4:"keep";}}')
		);
	}

	/**
	 * Test the Input::unserialize method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testUnserialize()
	{
		$this->markTestIncomplete();
	}

	/*
	 * Protected methods.
	 */

	/**
	 * Test the Input::loadAllInputs method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadAllInputs()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		include_once __DIR__ . '/stubs/JInputInspector.php';
		include_once __DIR__ . '/stubs/JFilterInputMock.php';
		include_once __DIR__ . '/stubs/JFilterInputMockTracker.php';

		$array = null;
		$this->class = new JInputInspector($array, array('filter' => new JFilterInputMock));
	}
}
<?php
require_once 'PHPUnit/Framework.php';

require_once JPATH_BASE . '/libraries/joomla/filesystem/path.php';
require_once JPATH_BASE . '/libraries/joomla/html/html.php';

/**
 * Test class for JHtml.
 * Generated by PHPUnit on 2009-10-27 at 15:36:23.
 */
class JHtmlTest extends JoomlaTestCase
{
	/**
	 * @var JHtml
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->saveFactoryState();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
	}

	/**
	 * Tests JHtml::calendar() method with and without 'readonly' attribute.
	 */
	public function testCalendar()
	{
		// Create a world for the test
		jimport('joomla.session.session');
		jimport('joomla.application.application');
		jimport('joomla.document.document');

		$cfg = new JObject();
		JFactory::$session = $this->getMock('JSession', array('_start'));
		JFactory::$application = $this->getMock('ApplicationMock');
		JFactory::$config = $cfg;

		JFactory::$application->expects($this->any())
								->method('getTemplate')
								->will($this->returnValue('atomic'));

		$cfg->live_site = 'http://example.com';
		$cfg->offset = 'Europe/Kiev';
		$_SERVER['HTTP_USER_AGENT'] = 'Test Browser';

		// two sets of test data
		$test_data = array('date' => '2010-05-28 00:00:00', 'friendly_date' => 'Friday, 28 May 2010',
					  'name' => 'cal1_name', 'id' => 'cal1_id', 'format' => '%Y-%m-%d',
					  'attribs' => array()
				);

		$test_data_ro = array_merge($test_data, array('attribs' => array('readonly' => 'readonly')));

		foreach (array($test_data, $test_data_ro) as $data)
		{
			// Reset the document
			JFactory::$document = JDocument::getInstance('html', array('unique_key' => serialize($data)));

			$input = JHtml::calendar($data['date'], $data['name'], $data['id'], $data['format'], $data['attribs']);

			$this->assertEquals(
				(string) $xml->input['title'],
				$data['friendly_date'],
				'Line:'.__LINE__.' The calendar input should have `title == "' . $data['friendly_date'] . '"`'
			);

			$this->assertEquals(
				(string) $xml->input['name'],
				$data['name'],
				'Line:'.__LINE__.' The calendar input should have `name == "' . $data['name'] . '"`'
			);

			$this->assertEquals(
				(string) $xml->input['id'],
				$data['id'],
				'Line:'.__LINE__.' The calendar input should have `id == "' . $data['id'] . '"`'
			);

			$head_data = JFactory::getDocument()->getHeadData();

			if (!isset($data['attribs']['readonly']) || !$data['attribs']['readonly'] === 'readonly')
			{
				$this->assertArrayHasKey(
					'/media/system/js/calendar.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar.js" should be loaded'
				);

				$this->assertArrayHasKey(
					'/media/system/js/calendar-setup.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar-setup.js" should be loaded'
				);
			}
		}
	}
}

class ApplicationMock
{
	public function getTemplate()
	{

	}
}

<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

use Piwik\Plugins\ShortcodeTracker\API;
/**
 * @group ShortcodeTracker
 * @group ShortcodeApi
 * @group Plugins
 */
class ShortcodeApiUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var API
     */
    private $component;

    public function setUp()
    {
        $this->component = new API();
    }

    /**
     *
	 *  @dataProvider urlProvider
     */
    public function testDecodeUrlForLocation($input, $expected)
    {
		$actual = $this->component->decodeUrlForLocation($input);
        $this->assertEquals($expected, $actual);
    }

	/**
	 * @return array
	 */
    public function urlProvider()
    {
        return array(
            array('http://www.johndoe.com', 'http://www.johndoe.com'),
            array('http://johndoe.com?p=1&q=3', 'http://johndoe.com?p=1&q=3'),
            array('http://johndoe.com?p=1&amp;q=3', 'http://johndoe.com?p=1&q=3')
        );
    }
}

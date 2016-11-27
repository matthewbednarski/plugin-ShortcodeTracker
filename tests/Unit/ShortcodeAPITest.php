<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\tests\Unit;

/**
 * @group ShortcodeTracker
 * @group ShortcodeApi
 * @group Plugins
 */
class ShortcodeApiTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
	 *  @dataProvider urlProvider
     */
    public function testDecodeUrlForLocation()
    {

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

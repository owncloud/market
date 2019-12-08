<?php
/**
 * @author Viktar Dubiniuk <dubinuk@owncloud.com>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Market\Tests\Unit;

use OCA\Market\VersionHelper;
use Test\TestCase;

class VersionHelperTest extends TestCase {
	/** @var VersionHelper | \PHPUnit\Framework\MockObject\MockObject */
	private $versionHelper;

	protected function setUp(): void {
		$this->versionHelper = new VersionHelper();
	}
	/**
	 * @dataProvider dataTestIsSameMajorVersion
	 *
	 * @param string $first
	 * @param string $second
	 * @param bool $expected
	 */
	public function testIsSameMajorVersion($first, $second, $expected) {
		$sameMajor = $this->versionHelper->isSameMajorVersion($first, $second);
		$this->assertEquals($expected, $sameMajor);
	}

	public function dataTestIsSameMajorVersion() {
		return [
			['1.0', '1.1', true],
			['1.0', '1.1', true],
			['1.0', null, false],
			['2.1.2', '1.2.3', false],
			['5.1', '5.2.3', true],
		];
	}

	/**
	 * @dataProvider dataTestCompare
	 *
	 * @param string $first
	 * @param string $second
	 * @param bool $expected
	 */
	public function testCompare($first, $second, $expected) {
		$result = $this->versionHelper->compare($first, $second, '>');
		$this->assertEquals($expected, $result);
	}

	public function dataTestCompare() {
		return [
			['1.0.2', '1.1', false],
			['2.3.4', '1.1', true],
			['1.0', null, true],
			['2.1.2', '1.2.3.4', true],
			['5.1', '5.2.3', false],
		];
	}
}

<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2017, ownCloud GmbH
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

namespace OCA\Market\Tests\Unit\Command;

use OCA\Market\Command\ListApps;
use OCA\Market\MarketService;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;

class ListAppsTest extends TestCase {

	/** @var CommandTester */
	private $commandTester;
	/** @var MarketService | \PHPUnit_Framework_MockObject_MockObject */
	private $marketService;

	public function setUp() {
		parent::setUp();

		$this->marketService = $this->createMock(MarketService::class);
		$command = new ListApps($this->marketService);
		$this->commandTester = new CommandTester($command);
	}

	public function testListApps() {
		$this->marketService->expects($this->once())->method('listApps')->willReturn([
			['id' => 'foo'],
			['id' => 'bar'],
		]);
		$this->commandTester->execute([]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains("bar\nfoo", $output);
	}
}

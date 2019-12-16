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

use OCA\Market\Command\UnInstallApp;
use OCA\Market\MarketService;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;

class UnInstallAppTest extends TestCase {

	/** @var CommandTester */
	private $commandTester;
	/** @var MarketService | \PHPUnit\Framework\MockObject\MockObject */
	private $marketService;

	public function setUp(): void {
		parent::setUp();

		$this->marketService = $this->createMock(MarketService::class);
		$command = new UnInstallApp($this->marketService);
		$this->commandTester = new CommandTester($command);
	}

	/**
	 */
	public function testInstallNotSupported() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Installing apps is not supported because the app folder is not writable.');

		$this->marketService->expects($this->once())->method('canInstall')->willReturn(false);
		$this->commandTester->execute([]);
	}

	public function testNothingToDo() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->commandTester->execute([]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('No appIds specified. Nothing to do.', $output);
	}

	public function testUnInstall() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('uninstallApp');
		$this->commandTester->execute([
			'ids' => ['foo']
		]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('foo: Un-Installing ...', $output);
		$this->assertContains('foo: App uninstalled.', $output);
	}
}

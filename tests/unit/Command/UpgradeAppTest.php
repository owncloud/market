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

use OCA\Market\Command\UpgradeApp;
use OCA\Market\MarketService;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;

class UpgradeAppTest extends TestCase {

	/** @var CommandTester */
	private $commandTester;
	/** @var MarketService | \PHPUnit_Framework_MockObject_MockObject */
	private $marketService;

	public function setUp() {
		parent::setUp();

		$this->marketService = $this->createMock(MarketService::class);
		$command = new UpgradeApp($this->marketService);
		$this->commandTester = new CommandTester($command);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Installing apps is not supported because the app folder is not writable.
	 */
	public function testInstallNotSupported() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(false);
		$this->commandTester->execute([]);
	}

	public function testNothingToDo() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->commandTester->execute([]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('No appId or path to a local package specified. Nothing to do.', $output);
	}

	public function testUpdateUnknownApp() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('isAppInstalled')->willReturn(false);
		$this->marketService->expects($this->never())->method('installApp');
		$this->marketService->expects($this->never())->method('updateApp');
		$this->commandTester->execute([
			'ids' => ['foo']
		]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('foo: Not installed ...', $output);
	}

	public function testUpdateNoNewVersion() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('isAppInstalled')->willReturn(true);
		$this->marketService->expects($this->once())->method('getAvailableUpdateVersion')->willReturn(false);
		$this->marketService->expects($this->never())->method('installApp');
		$this->marketService->expects($this->never())->method('updateApp');
		$this->commandTester->execute([
			'ids' => ['foo']
		]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('foo: No update available', $output);
	}

	public function testUpdateApp() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('isAppInstalled')->willReturn(true);
		$this->marketService->expects($this->once())->method('getAvailableUpdateVersion')->willReturn('1.2.3');
		$this->marketService->expects($this->never())->method('installApp');
		$this->marketService->expects($this->once())->method('updateApp');
		$this->commandTester->execute([
			'ids' => ['foo']
		]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains('foo: Installing new version 1.2.3 ...', $output);
	}

	/**
	 * @dataProvider providesVersions
	 * @param bool $withHigherVersion
	 */
	public function testLocalUpdate($withHigherVersion) {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('readAppPackage')->willReturn([
			'id' => 'bla',
			'version' => $withHigherVersion ? '1.2.2' : '1.2.1'
		]);
		$this->marketService->expects($this->once())->method('isAppInstalled')->willReturn(true);
		$this->marketService->expects($this->once())->method('getInstalledAppInfo')->willReturn([
			'version' => '1.2.1'
		]);
		$this->marketService->expects($withHigherVersion ? $this->once() : $this->never())->method('updatePackage');
		$this->commandTester->execute([
			'-l' => ['bla.tar.gz']
		]);
		$output = $this->commandTester->getDisplay();
		if ($withHigherVersion) {
			$this->assertContains('bla: Installing new version from bla.tar.gz', $output);
			$this->assertContains('bla: App updated.', $output);
		} else {
			$this->assertContains('bla: bla.tar.gz has the same or older version of the app', $output);
		}
	}

	public function providesVersions() {
		return [
			[true],
			[false],
		];
	}

	public function testListOption() {
		$this->marketService->expects($this->once())->method('canInstall')->willReturn(true);
		$this->marketService->expects($this->once())->method('getUpdates')->willReturn([
			'foo' => ['version' => '1.2.3'],
			'bar' => ['version' => '4.0.0'],
		]);
		$this->commandTester->execute([
			'--list' => true
		]);
		$output = $this->commandTester->getDisplay();
		$this->assertContains("foo : 1.2.3\nbar : 4.0.0", $output);
	}
}

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

use OCA\Market\HttpService;
use OCA\Market\VersionHelper;
use OCP\App\AppManagerException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\Http\Client\IResponse;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use Test\TestCase;

/**
 * Class HttpServiceTest
 *
 * @package OCA\Market\Tests\Unit
 */
class HttpServiceTest extends TestCase {
	/** @var IClientService | \PHPUnit\Framework\MockObject\MockObject */
	private $httpClientService;
	/** @var VersionHelper | \PHPUnit\Framework\MockObject\MockObject */
	private $versionHelper;
	/** @var ICacheFactory | \PHPUnit\Framework\MockObject\MockObject */
	private $cacheFactory;
	/** @var IConfig | \PHPUnit\Framework\MockObject\MockObject */
	private $config;
	/** @var IL10N | \PHPUnit\Framework\MockObject\MockObject */
	private $l10n;
	/** @var HttpService */
	private $httpService;

	protected function setUp(): void {
		parent::setUp();
		$this->httpClientService = $this->createMock(IClientService::class);
		$this->versionHelper =  $this->createMock(VersionHelper::class);
		$this->cacheFactory = $this->createMock(ICacheFactory::class);
		$this->config = $this->createMock(IConfig::class);
		$this->l10n = $this->createMock(IL10N::class);
		$this->httpService = new HttpService(
			$this->httpClientService,
			$this->versionHelper,
			$this->config,
			$this->cacheFactory,
			$this->l10n
		);
	}

	/**
	 * @dataProvider checkInternetConnectionDataProvider
	 */
	public function testCheckInternetConnection($connectionStatus, $expectedExceptionClass) {
		$this->config->method('getSystemValue')
			->with('has_internet_connection', true)
			->willReturn($connectionStatus);
		if ($expectedExceptionClass !== '') {
			$this->expectException($expectedExceptionClass);
		}
		$this->assertNull($this->httpService->checkInternetConnection());
	}

	public function checkInternetConnectionDataProvider() {
		return [
			[true, ''],
			[false, AppManagerException::class]
		];
	}

	public function testGetApps() {
		$expectedApps = [];
		$this->config
			->expects($this->any())
			->method('getSystemValue')
			->withConsecutive(
				['has_internet_connection', true],
				['marketplace.key', null],
			)
			->willReturnOnConsecutiveCalls(true, '');

		$clientMock = $this->getClientResponseMockForGet(\json_encode($expectedApps));
		$this->httpClientService->method('newClient')->willReturn($clientMock);
		$apps = $this->httpService->getApps();
		$this->assertEquals($expectedApps, $apps);
	}

	private function getClientResponseMockForGet($body) {
		$responseMock = $this->createMock(IResponse::class);
		$responseMock->method('getBody')->willReturn($body);
		$clientMock = $this->createMock(IClient::class);
		$clientMock->method('get')->willReturn($responseMock);
		return $clientMock;
	}

	private function getClientResponseMockForPost($body) {
		$responseMock = $this->createMock(IResponse::class);
		$responseMock->method('getBody')->willReturn($body);
		$clientMock = $this->createMock(IClient::class);
		$clientMock->method('post')->willReturn($responseMock);
		return $clientMock;
	}
}

<?php

namespace OCA\Market\Tests\Unit;

use OCA\Market\HttpService;
use OCP\App\AppManagerException;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\App\IAppManager;
use OCA\Market\MarketService;
use Test\TestCase;

class MarketServiceTest extends TestCase {

	/** @var MarketService */
	private $marketService;
	/** @var HttpService | \PHPUnit_Framework_MockObject_MockObject $cacheFactoryMock */
	private $httpService;
	/** @var IAppManager | \PHPUnit_Framework_MockObject_MockObject */
	private $appManager;
	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject $configMock */
	private $config;

	public function setUp() {
		/** @var ICacheFactory | \PHPUnit_Framework_MockObject_MockObject $cacheFactoryMock */
		$this->httpService = $this->createMock(HttpService::class);
		$this->appManager = $this->createMock(IAppManager::class);
		$this->appManager->method('getAllApps')->willReturn([]);
		$this->config = $this->createMock(IConfig::class);
		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10nMock */
		$l10nMock = $this->createMock(IL10N::class);
		$l10nMock->method('t')->willReturnArgument(0);
		$this->marketService = new MarketService(
			$this->httpService,
			$this->appManager,
			$this->config,
			$l10nMock
		);
	}

	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testInstallWithInternetConnectionDisabled(){
		$this->appManager->method('canInstall')->willReturn(true);
		$this->httpService->method('getApps')->willThrowException(
			new AppManagerException()
		);
		$this->marketService->installApp('fubar');
	}
	
	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testUpdateWithInternetConnectionDisabled(){
		$this->appManager->method('canInstall')->willReturn(true);
		$this->marketService->updateApp('files');
	}

	/**
	 * @dataProvider providesMarketMethods
	 * @expectedException \Exception
	 * @expectedExceptionMessage Installing apps is not supported because the app folder is not writable.
	 */
	public function testInstallNotPossible($method) {
		$this->appManager->method('canInstall')->willReturn(false);
		$this->marketService->$method('test');
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Please enter a license-key in to config.php
	 */
	public function testInstallAppChecksLicenseOnLatestRelease() {
		$this->appManager->method('canInstall')->willReturn(true);
		$this->httpService->method('getApps')->willReturn(
			[
				'some_app' => [
					'id' => 'some_app',
					'releases' => [
						['license' => 'ownCloud Commercial License'],
						['license' => 'agplv2']
					]
				]
			]
		);
		$this->marketService->installApp('some_app');
	}

	public function providesMarketMethods() {
		return [
			['installApp'],
			['uninstallApp'],
			['updateApp']
		];
	}
}

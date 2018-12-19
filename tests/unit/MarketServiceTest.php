<?php

namespace OCA\Market\Tests\Unit;

use OCA\Market\HttpService;
use OCA\Market\VersionHelper;
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
	/** @var VersionHelper | \PHPUnit_Framework_MockObject_MockObject $cacheFactoryMock */
	private $versionHelper;
	/** @var IAppManager | \PHPUnit_Framework_MockObject_MockObject */
	private $appManager;
	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject $configMock */
	private $config;

	public function setUp() {
		$this->httpService = $this->createMock(HttpService::class);
		$this->versionHelper = $this->createMock(VersionHelper::class);
		$this->appManager = $this->createMock(IAppManager::class);
		$this->config = $this->createMock(IConfig::class);
		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10nMock */
		$l10nMock = $this->createMock(IL10N::class);
		$l10nMock->method('t')->willReturnArgument(0);
		$this->marketService = new MarketService(
			$this->httpService,
			$this->versionHelper,
			$this->appManager,
			$this->config,
			$l10nMock
		);
	}

	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testInstallWithInternetConnectionDisabled() {
		$this->appManager->method('getAllApps')->willReturn([]);
		$this->appManager->method('canInstall')->willReturn(true);
		$this->httpService->method('getApps')->willThrowException(
			new AppManagerException()
		);
		$this->marketService->installApp('fubar');
	}
	
	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testUpdateWithInternetConnectionDisabled() {
		$this->appManager->method('getAllApps')->willReturn([]);
		$this->appManager->method('canInstall')->willReturn(true);
		$this->marketService->updateApp('files');
	}

	/**
	 * @dataProvider providesMarketMethods
	 * @expectedException \Exception
	 * @expectedExceptionMessage Installing apps is not supported because the app folder is not writable.
	 */
	public function testInstallNotPossible($method) {
		$this->appManager->method('getAllApps')->willReturn([]);
		$this->appManager->method('canInstall')->willReturn(false);
		$this->marketService->$method('test');
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Please enter a license-key in to config.php
	 */
	public function testInstallAppChecksLicenseOnLatestRelease() {
		$this->appManager->method('getAllApps')->willReturn([]);
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

	public function testUpdateToVersion() {
		$appId = 'some_app';
		$this->appManager->expects($this->any())
			->method('canInstall')
			->willReturn(true);
		$this->appManager->expects($this->any())
			->method('getAllApps')
			->willReturn([$appId]);
		$this->appManager->expects($this->any())
			->method('getAppInfo')
			->willReturn(
					['id' => $appId, 'version' => '1.1']
			);
		$this->httpService->expects($this->any())
			->method('getApps')
			->willReturn(
				[
					[	'id' => $appId,
						'releases' => [
							[
								'version' => '1.1',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
							[
								'version' => '1.2.3',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'hit'
							],
							[
								'version' => '1.3',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
						]
					]
				]
			);
		$this->httpService->expects($this->once())->method('downloadApp')
			->with('hit', $this->anything());

		$this->versionHelper->method('compare')->willReturn(false);
		$this->marketService->updateApp('some_app', '1.2.3');
	}

	public function testRecentVersionIsInstalled() {
		$appId = 'some_app';
		$this->appManager->expects($this->any())
			->method('canInstall')
			->willReturn(true);
		$this->appManager->expects($this->any())
			->method('getAllApps')
			->willReturn([]);
		$this->appManager->expects($this->any())
			->method('getAppInfo')
			->willReturn([]);
		$this->httpService->expects($this->any())
			->method('getApps')
			->willReturn(
				[
					[	'id' => $appId,
						'releases' => [
							[
								'version' => '1.1',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
							[
								'version' => '3.2.3',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'hit'
							],
							[
								'version' => '2.3.0',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
						]
					]
				]
			);
		$this->httpService->expects($this->once())->method('downloadApp')
			->with('hit', $this->anything());

		$this->versionHelper->method('compare')->willReturn(false);
		$this->marketService->installApp($appId);
	}

	public function testGetUpdateVersions() {
		$versionHelper = new VersionHelper();
		$appId = 'some_app';
		$this->appManager->method('canInstall')
			->willReturn(true);
		$this->appManager->method('getAllApps')
			->willReturn([$appId]);
		$this->appManager->method('getAppInfo')
			->willReturn(
				['id' => $appId, 'version' => '1.1']
			);
		$this->httpService->method('getApps')
			->willReturn(
				[
					[	'id' => $appId,
						'releases' => [
							[
								'version' => '1.1',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
							[
								'version' => '1.1.1',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
							[
								'version' => '3.2.3',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'hit'
							],
							[
								'version' => '2.3.0',
								'license' => 'agpl',
								'platformMin' => null,
								'platformMax' => null,
								'download' => 'miss'
							],
						]
					]
				]
			);
		$this->versionHelper->method('isSameMajorVersion')
			->willReturnCallback([$versionHelper, 'isSameMajorVersion']);
		$updatesAvailable = $this->marketService->getAvailableUpdateVersions($appId);
		$this->assertEquals('1.1.1', $updatesAvailable['minor']);
		$this->assertEquals('3.2.3', $updatesAvailable['major']);
	}
}

<?php

namespace OCA\Market\Tests\Unit;

use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\App\IAppManager;
use OCA\Market\MarketService;
use Test\TestCase;

class MarketServiceTest extends TestCase {

	/** @var MarketService */
	private $marketService;
	/** @var boolean */
	private $hasInternetConnection;
	/** @var IAppManager | \PHPUnit_Framework_MockObject_MockObject */
	private $appManager;

	public function setUp(){
		$this->hasInternetConnection = true;
		$this->appManager = $this->createMock(IAppManager::class);
		$this->appManager->method('getAllApps')->willReturn([]);

		/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject $configMock */
		$configMock = $this->getConfigMock();
		/** @var ICacheFactory | \PHPUnit_Framework_MockObject_MockObject $cacheFactoryMock */
		$cacheFactoryMock = $this->createMock(ICacheFactory::class);
		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10nMock */
		$l10nMock = $this->createMock(IL10N::class);
		$this->marketService = new MarketService(
			$this->appManager,
			$configMock,
			$cacheFactoryMock,
			$l10nMock
		);
	}

	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testInstallWithInternetConnectionDisabled(){
		$this->hasInternetConnection = false;
		$this->appManager->method('canInstall')->willReturn(true);
		$this->marketService->installApp('fubar');
	}
	
	/**
	 * @expectedException \OCP\App\AppManagerException
	*/
	public function testUpdateWithInternetConnectionDisabled(){
		$this->hasInternetConnection = false;
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

	public function providesMarketMethods() {
		return [
			['installApp'],
			['uninstallApp'],
			['updateApp']
		];
	}
	
	public function getSystemValue($configKey, $default = null){
		if ($configKey==='has_internet_connection'){
			return $this->hasInternetConnection;
		}
		return \OC::$server->getConfig()->getSystemValue($configKey, $default);
	}
	
	private function getConfigMock(){
		$config = $this->createMock(IConfig::class);
		$config->method('getSystemValue')
				->will($this->returnCallback([$this, 'getSystemValue']));
		return $config;
	}
}

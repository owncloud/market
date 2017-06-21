<?php

use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\App\IAppManager;
use OCA\Market\MarketService;
use Test\TestCase;

class MarketServiceTest extends TestCase {

	private $marketService;
	private $hasInternetConnection;

	public function setUp(){
		$this->hasInternetConnection = true;
		$appManagerMock = $this->getMockBuilder(IAppManager::class)->getMock();
		$appManagerMock->method('getAllApps')->will($this->returnValue([]));
		
		$configMock = $this->getConfigMock();
		$cacheFactoryMock = $this->getMockBuilder(ICacheFactory::class)->getMock();
		$l10nMock = $this->getMockBuilder(IL10N::class)->getMock();
		$this->marketService = new MarketService(
			$appManagerMock,
			$configMock,
			$cacheFactoryMock,
			$l10nMock
		);
	}

	/**
	 * @expectedException OCP\App\AppManagerException
	*/
	public function testInstallWithInternetConnectionDisabled(){
		$this->hasInternetConnection = false;
		$this->marketService->installApp('fubar');
	}
	
	/**
	 * @expectedException OCP\App\AppManagerException
	*/
	public function testUpdateWithInternetConnectionDisabled(){
		$this->hasInternetConnection = false;
		$this->marketService->updateApp('files');
	}
	
	public function getSystemValue($configKey, $default = null){
		if ($configKey==='has_internet_connection'){
			return $this->hasInternetConnection;
		}
		return \OC::$server->getConfig()->getSystemValue($configKey, $default);
	}
	
	private function getConfigMock(){
		$config = $this->getMockBuilder(IConfig::class)
			->setMethods([
				'getSystemValue',
				'setSystemValue',
				'getSystemValues',
				'setSystemValues',
				'getFilteredSystemValue',
				'deleteSystemValue',
				'getAppKeys',
				'setAppValue',
				'getAppValue',
				'deleteAppValue',
				'deleteAppValues',
				'setUserValue',
				'getUserValue',
				'getUserValueForUsers',
				'getUserKeys',
				'deleteUserValue',
				'deleteAllUserValues',
				'deleteAppFromAllUsers',
				'getUsersForUserValue',
			])
			->getMock();

		$config->method('getSystemValue')
				->will($this->returnCallback([$this, 'getSystemValue']));
		return $config;
	}
}

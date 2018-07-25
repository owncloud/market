<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH
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

namespace OCA\Market;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use OC\App\DependencyAnalyzer;
use OC\App\Platform;
use OCA\Market\Exception\LicenseKeyAlreadyAvailableException;
use OCA\Market\Exception\MarketException;
use OCP\App\AppManagerException;
use OCP\App\IAppManager;
use OCP\ICacheFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Util;
use OCP\App\AppAlreadyInstalledException;
use OCP\App\AppNotFoundException;
use OCP\App\AppNotInstalledException;
use OCP\App\AppUpdateNotFoundException;

class MarketService {

	/** @var array */
	private $apps;
	/** @var ICacheFactory */
	private $cacheFactory;
	/** @var IAppManager */
	private $appManager;
	/** @var IConfig */
	private $config;
	/** @var string */
	private $storeUrl;
	/** @var array */
	private $categories;
	/** @var array */
	private $bundles;
	/** @var IL10N */
	private $l10n;

	/**
	 * Service constructor.
	 *
	 * @param IAppManager $appManager
	 * @param IConfig $config
	 * @param ICacheFactory $cacheFactory
	 * @param IL10N $l10n
	 */
	public function __construct(IAppManager $appManager, IConfig $config, ICacheFactory $cacheFactory, IL10N $l10n) {
		$storeUrl = $config->getSystemValue('appstoreurl', 'https://marketplace.owncloud.com');

		$this->appManager = $appManager;
		$this->config = $config;
		$this->storeUrl = rtrim($storeUrl, '/');
		$this->cacheFactory = $cacheFactory;
		$this->l10n = $l10n;
	}

	/**
	 * Install an app for the given app id
	 *
	 * @param string $appId
	 * @param bool $skipMigrations whether to skip migrations
	 * @throws AppAlreadyInstalledException
	 * @throws AppManagerException
	 * @throws \Exception
	 */
	public function installApp($appId, $skipMigrations = false) {
		if (!$this->canInstall()) {
			throw new \Exception("Installing apps is not supported because the app folder is not writable.");
		}

		$availableReleases = array_column($this->getApps(), 'releases', 'id')[$appId];
		if (array_shift($availableReleases)['license'] === 'ownCloud Commercial License') {
			$license = $this->getLicenseKey();
			if ($license === null) {
				throw new \Exception($this->l10n->t('Please enter a license-key in to config.php'));
			}
			if ($appId !== 'enterprise_key') {
				if (!$this->appManager->isEnabledForUser('enterprise_key')) {
					throw new \Exception($this->l10n->t('Please install and enable the enterprise_key app and enter a license-key in config.php first.'));
				}
				if (class_exists('\OCA\Enterprise_Key\EnterpriseKey')) {
					$e = new \OCA\Enterprise_Key\EnterpriseKey($license, $this->config);
					if (!$e->check()) {
						throw new \Exception($this->l10n->t('Your license-key is not valid.'));
					}
				}
			}
		}


		$info = $this->getInstalledAppInfo($appId);
		if (!is_null($info)) {
			throw new AppAlreadyInstalledException($this->l10n->t('App %s is already installed', $appId));
		}

		// download package
		$package = $this->downloadPackage($appId);
		$this->installPackage($package, $skipMigrations);
		$this->appManager->enableApp($appId);
	}

	/**
	 * Install downloaded package
	 * @param string $package package path
	 * @param bool $skipMigrations whether to skip migrations
	 * @return string appId
	 */
	public function installPackage($package, $skipMigrations = false){
		return $this->appManager->installApp($package, $skipMigrations);
	}

	/**
	 * Get appinfo from package
	 * @param string $path
	 * @return string[] app info
	 */
	public function readAppPackage($path){
		return $this->appManager->readAppPackage($path);
	}

	private function downloadPackage($appId) {
		$this->checkInternetConnection();
		$data = $this->getAppInfo($appId);
		if (empty($data)) {
			throw new AppNotFoundException($this->l10n->t('Unknown app (%s)', $appId));
		}

		$version = $this->getPlatformVersion();
		$release = array_filter($data['releases'], function($element) use ($version) {
			$platformMin = $element['platformMin'];
			$platformMax = $element['platformMax'];
			$tooSmall = $this->compare($version, $platformMin, '<');
			$tooBig = $this->compare($version, $platformMax, '>');

			return $tooSmall === false && $tooBig === false;
		});
		if (empty($release)) {
			throw new AppUpdateNotFoundException($this->l10n->t('No compatible version for %s', $appId));
		}
		usort($release, function($a, $b) {
			return version_compare($b['version'], $a['version']);
		});
		$release = $release[0];
		$downloadLink = $release['download'];

		$pathInfo = pathinfo($downloadLink);
		$extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
		$path = \OC::$server->getTempManager()->getTemporaryFile($extension);
		$this->httpGet($downloadLink, ['save_to' => $path]);
		return $path;
	}

	/**
	 * Checks if the app with the given app id is installed
	 *
	 * @param string $appId
	 * @return bool
	 */
	public function isAppInstalled($appId) {
		$info = $this->getInstalledAppInfo($appId);
		return !is_null($info);
	}

	/**
	 * Returns the version for the app if an update is available
	 *
	 * @param string $appId
	 * @return bool|string
	 * @throws AppNotFoundException
	 * @throws AppNotInstalledException
	 */
	public function getAvailableUpdateVersion($appId) {
		$info = $this->getInstalledAppInfo($appId);
		if (is_null($info)) {
			throw new AppNotInstalledException($this->l10n->t('App (%s) is not installed', $appId));
		}
		$marketInfo = $this->getAppInfo($appId);
		if (is_null($marketInfo)) {
			throw new AppNotFoundException($this->l10n->t('App (%s) is not known at the marketplace.', $appId));
		}
		$releases = $marketInfo['releases'];
		$currentVersion = (string) $info['version'];
		$releases = array_filter($releases, function($r) use ($currentVersion) {
			$marketVersion = $r['version'];
			return version_compare($marketVersion, $currentVersion, '>');
		});
		usort($releases, function ($a, $b) {
			return version_compare($a['version'], $b['version'], '>');
		});
		if (!empty($releases)) {
			return array_pop($releases)['version'];
		}
		return false;
	}

	public function getAppInfo($appId) {
		$data = $this->getApps();
		$data = array_filter($data, function($element) use ($appId) {
			return $element['id'] === $appId;
		});
		if (empty($data)) {
			return null;
		}
		return reset($data);
	}

	/**
	 * @param string $appId
	 * @return array|null
	 */
	public function getInstalledAppInfo($appId) {
		$apps = $this->appManager->getAllApps();
		foreach ($apps as $app) {
			$info = $this->appManager->getAppInfo($app);
			if (isset($info['id']) && $info['id'] === $appId) {
				return $info;
			}
		}

		return null;
	}

	/**
	 * Update the app
	 *
	 * @param string $appId
	 * @throws AppManagerException
	 * @throws AppNotInstalledException
	 */
	public function updateApp($appId) {
		if (!$this->canInstall()) {
			throw new \Exception("Installing apps is not supported because the app folder is not writable.");
		}

		$info = $this->getInstalledAppInfo($appId);
		if (is_null($info)) {
			throw new AppNotInstalledException($this->l10n->t('App (%s) is not installed', $appId));
		}

		// download package
		$package = $this->downloadPackage($appId);
		$this->updatePackage($package);
	}

	/**
	 * Uninstall the app
	 *
	 * @param string $appId
	 * @throws AppManagerException
	 */
	public function uninstallApp($appId) {
		if (!$this->canInstall()) {
			throw new \Exception("Installing apps is not supported because the app folder is not writable.");
		}

		if ($this->appManager->isShipped($appId)) {
			throw new AppManagerException($this->l10n->t('Shipped apps cannot be uninstalled'));
		}
		if (!\OC_App::removeApp($appId)) {
			throw new AppManagerException($this->l10n->t('App (%s) could not be uninstalled. Please check the server logs.', $appId));
		}
	}

	/**
	 * Update downloaded package
	 * @param string $package
	 * @return string appId
	 */
	public function updatePackage($package){
		return $this->appManager->updateApp($package);
	}

	/**
	 * Verify if all requirements are met
	 *
	 * @param [] $appInfo
	 * @return array []
	 */
	public function getMissingDependencies($appInfo) {
		// bad hack - should use OCP
		$l10n = \OC::$server->getL10N('settings');
		$dependencyAnalyzer = new DependencyAnalyzer(new Platform($this->config), $l10n);

		return $dependencyAnalyzer->analyze($appInfo);
	}

	/**
	 * Get apps which need to be updated
	 *
	 * @return array
	 */
	public function getUpdates() {
		$result = [];
		$apps = $this->appManager->getAllApps();
		foreach ($apps as $app) {
			$info = $this->appManager->getAppInfo($app);
			if (isset($info['id'])) {
				try {
					$appId = $info['id'];
					$newVersion = $this->getAvailableUpdateVersion($appId);
					if ($newVersion) {
						$result[$app] = [
							'version' => $newVersion,
							'id' => $appId
						];
					}
				} catch (AppNotInstalledException $e) {
					// ignore exceptions thrown by getAvailableUpdateVersion
				} catch (AppNotFoundException $e) {
					// app is not published at marketplace - this is ok
				}

			}
		}

		return $result;
	}

	/**
	 * Truncates both versions to the lowest common version, e.g.
	 * 5.1.2.3 and 5.1 will be turned into 5.1 and 5.1,
	 * 5.2.6.5 and 5.1 will be turned into 5.2 and 5.1
	 * @param string $first
	 * @param string $second
	 * @return string[] first element is the first version, second element is the
	 * second version
	 */
	private function normalizeVersions($first, $second) {
		$first = explode('.', $first);
		$second = explode('.', $second);

		// get both arrays to the same minimum size
		$length = min(count($second), count($first));
		$first = array_slice($first, 0, $length);
		$second = array_slice($second, 0, $length);

		return [implode('.', $first), implode('.', $second)];
	}

	/**
	 * Parameters will be normalized and then passed into version_compare
	 * in the same order they are specified in the method header
	 * @param string $first
	 * @param string $second
	 * @param string $operator
	 * @return bool result similar to version_compare
	 */
	private function compare($first, $second, $operator) {
		// we can't normalize versions if one of the given parameters is not a
		// version string but null. In case one parameter is null normalization
		// will therefore be skipped
		if ($first !== null && $second !== null) {
			list($first, $second) = $this->normalizeVersions($first, $second);
		}

		return version_compare($first, $second, $operator);
	}

	private function getPlatformVersion() {
		$v = Util::getVersion();
		return join('.', $v);
	}

	private function getApps() {
		$version = $this->getPlatformVersion();
		list($version,) = $this->normalizeVersions($version, '1.2.3');
		if (!is_null($this->apps)) {
			return $this->apps;
		}

		return $this->queryData("apps_$version", "/api/v1/platform/$version/apps.json");
	}

	public function getBundles() {
		if ($this->bundles !== null) {
			return $this->bundles;
		}
		$this->bundles = $this->queryData("bundles", "/api/v1/bundles.json");
		return $this->bundles;
	}


	public function getApiKey() {
		$configFileApiKey = $this->config->getSystemValue('marketplace.key', null);

		if ($configFileApiKey) {
			return $configFileApiKey;
		}

		return $this->config->getAppValue('market', 'key', null);
	}

	public function setApiKey($apiKey) {
		if ($this->isApiKeyChangeableByUser()) {
			$this->config->setAppValue('market', 'key', $apiKey);
			$this->invalidateCache();
			return true;
		}

		return false;
	}

	/**
	 * ApiKey can only be changed by user if no key is configured in config.php
	 * @return bool
	 */
	public function isApiKeyChangeableByUser() {
		$configFileApiKey = $this->config->getSystemValue('marketplace.key', null);

		if ($configFileApiKey) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @param array $options
	 * @param string | null $apiKey
	 * @return \OCP\Http\Client\IResponse
	 */
	private function httpGet($path, $options = [], $apiKey = null) {
		if ($apiKey === null) {
			$apiKey = $this->getApiKey();
		}
		if ($apiKey !== null) {
			$options = array_merge([
				'headers' => ['Authorization' => "apikey: $apiKey"]
			], $options);
		}
		$ca = $this->config->getSystemValue('marketplace.ca', null);
		if ($ca !== null) {
			$options = array_merge([
				'verify' => $ca
			], $options);
		}
		$client = \OC::$server->getHTTPClientService()->newClient();
		try {
			$response = $client->get($path, $options);
		} catch (TransferException $e) {
			if ($e instanceof ClientException) {
				if ($e->getCode() === 401) {
					if ($apiKey !== null) {
						throw new AppManagerException($this->l10n->t('Invalid marketplace API key provided'));
					}
					throw new AppManagerException($this->l10n->t('Marketplace API key missing'));
				}
				if ($e->getCode() === 402) {
					throw new AppManagerException($this->l10n->t('Active subscription on marketplace required'));
				}
			}
			throw new AppManagerException($this->l10n->t('No marketplace connection: '. $e->getMessage()), 0, $e);
		}
		return $response;
	}

	public function listApps($category = null) {
		$apps = $this->getApps();
		if ($category !== null) {
			$apps = array_filter($apps, function ($app) use ($category) {
				return in_array($category, $app['categories']);
			});
		}
		return $apps;
	}

	public function getCategories() {
		if ($this->categories !== null) {
			return $this->categories;
		}

		$this->categories = $this->queryData('categories', "/api/v1/categories.json");
		return $this->categories;
	}

	private function queryData($key, $uri) {
		// read from cache
		if ($this->cacheFactory->isAvailable()) {
			$cache = $this->cacheFactory->create('ocmp');
			$data = $cache->get($key);
			if ($data !== null) {
				return json_decode($data, true);
			}
		}

		$this->checkInternetConnection();

		// ask the server
		$response = $this->httpGet($this->storeUrl . $uri);
		$data = $response->getBody();
		if ($this->cacheFactory->isAvailable()) {
			// cache if for a day - TODO: evaluate the response header
			$cache = $this->cacheFactory->create('ocmp');
			$cache->set($key, $data, 60*60*24);
		}
		return json_decode($data, true);

	}
	
	private function checkInternetConnection(){
		if ($this->config->getSystemValue('has_internet_connection', true) !== true){
			throw new AppManagerException(
				$this->l10n->t('The Internet connection is disabled.'
				)
			);
		}
	}

	/**
	 * @param string $apiKey
	 * @return bool
	 */
	public function isApiKeyValid($apiKey) {
		if ($apiKey === '') {
			return true;
		}
		try {
			$this->httpGet($this->storeUrl . '/api/v1/categories.json', [], $apiKey);
			return true;
		} catch (\Exception $ex) {
			return false;
		}
	}

	/**
	 * @return string|null
	 */
	private function getLicenseKey() {
		$licenseKey = $this->config->getSystemValue('license-key');

		if ($licenseKey) {
			return $licenseKey;
		}

		return $this->config->getAppValue('enterprise_key', 'license-key', null);
	}

	/**
	 * @return bool
	 */
	public function hasLicenseKey() {
		return $this->getLicenseKey() !== null;
	}

	/**
	 * @return string
	 * @throws LicenseKeyAlreadyAvailableException
	 * @throws MarketException
	 */
	public function requestLicenseKey() {
		if ($this->hasLicenseKey()) {
			throw new LicenseKeyAlreadyAvailableException();
		}

		$instanceId = $this->config->getSystemValue('instanceid');
		$data = $this->queryData(
			'demo_license_information',
			"/api/v1/instance/$instanceId/demo-key"
		);

		if (!array_key_exists('license_key', $data)) {
			throw new MarketException('Marketplace did not return a demo license key.');
		}

		$demoLicenseKey = $data['license_key'];

		if (!$demoLicenseKey) {
			throw new MarketException('Marketplace returned an empty demo license key.');
		}

		$this->config->setAppValue('enterprise_key', 'license-key', $demoLicenseKey);
		$this->invalidateCache();
		return $demoLicenseKey;
	}

	public function canInstall() {
		if (!method_exists($this->appManager, 'canInstall')) {
			$appsFolder = \OC_App::getInstallPath();
			return $appsFolder !== null && is_writable($appsFolder) && is_readable($appsFolder);
		}

		return $this->appManager->canInstall();
	}

	public function invalidateCache() {
		if (!$this->cacheFactory->isAvailable()) {
			return;
		}
		$cache = $this->cacheFactory->create('ocmp');
		$cache->clear();
	}
}

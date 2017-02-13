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


namespace OCA\Market\Marketplace;

use OCP\App\IAppManager;
use OCP\IAppConfig;
use OCP\ICacheFactory;
use OCP\Util;

class Service {

	/** @var array */
	private $apps;
	/** @var ICacheFactory */
	private $cacheFactory;
	/** @var IAppManager */
	private $appManager;
	/** @var IAppConfig */
	private $appConfig;
	/** @var string */
	private $storeUrl;

	/**
	 * Service constructor.
	 *
	 * @param IAppManager $appManager
	 * @param IAppConfig $appConfig
	 * @param ICacheFactory $cacheFactory
	 * @param string $storeUrl
	 */
	public function __construct(IAppManager $appManager, IAppConfig $appConfig, ICacheFactory $cacheFactory, $storeUrl) {
		$this->appManager = $appManager;
		$this->appConfig = $appConfig;
		$this->storeUrl = $storeUrl;
		$this->cacheFactory = $cacheFactory;
	}

	/**
	 * Install an app for the given app id
	 * @param string $appId
	 */
	public function installApp($appId) {

		$info = $this->getInstalledAppInfo($appId);
		if (!is_null($info)) {
			throw new \InvalidArgumentException('App is already installed');
		}

		// download package
		$package = $this->downloadPackage($appId);
		$this->appManager->installApp($package);
	}

	private function downloadPackage($appId) {

		$data = $this->getAppInfo($appId);
		if (empty($data)) {
			throw new \Exception('Unknown app');
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
			throw new \Exception('No matching version');
		}
		$release = $release[0];
		$downloadLink = $release['download'];

		$pathInfo = pathinfo($downloadLink);
		$extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
		$path = \OC::$server->getTempManager()->getTemporaryFile($extension);
		$client = $this->newHttpClient();
		$client->get($downloadLink, ['save_to' => $path]);

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
	 */
	public function getAvailableUpdateVersion($appId) {
		$info = $this->getInstalledAppInfo($appId);
		if (is_null($info)) {
			throw new \InvalidArgumentException("App ($appId) is not installed");
		}
		$marketInfo = $this->getAppInfo($appId);
		if (is_null($marketInfo)) {
			throw new \InvalidArgumentException("App ($appId) is not known at the marketplace.");
		}
		$marketVersion = (string) $marketInfo['version'];
		$currentVersion = (string) $info['version'];
		if (version_compare($marketVersion, $currentVersion, '>')) {
			return $marketVersion;
		}
		return false;
	}

	private function getAppInfo($appId) {
		$data = $this->getApps();
		$data = array_filter($data, function($element) use ($appId) {
			return $element['id'] === $appId;
		});
		if (empty($data)) {
			return null;
		}
		return $data[0];
	}

	private function getInstalledAppInfo($appId) {
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
	 */
	public function updateApp($appId) {
		$info = $this->getInstalledAppInfo($appId);
		if (is_null($info)) {
			throw new \InvalidArgumentException('App is not installed');
		}

		// download package
		$package = $this->downloadPackage($appId);
		$this->appManager->updateApp($package);
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
				} catch (\InvalidArgumentException $ex) {
					// ignore exceptions thrown by getAvailableUpdateVersion
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
		// read from cache
		if ($this->cacheFactory->isAvailable()) {
			$cache = $this->cacheFactory->create('ocmp');
			$data = $cache->get("apps_$version");
			$this->apps = json_decode($data, true);
			return $this->apps;
		}

		// ask the server
		$client = $this->newHttpClient();
		$response = $client->get($this->storeUrl . "/api/v1/platform/$version/apps.json");
		$data = $response->getBody();
		if ($this->cacheFactory->isAvailable()) {
			// cache if for a day - TODO: evaluate the response header
			$cache = $this->cacheFactory->create('ocmp');
			$cache->set("apps_$version", $data, 60*60*24);
		}
		$this->apps = json_decode($data, true);
		return $this->apps;
	}

	/**
	 * @return \OCP\Http\Client\IClient
	 */
	private function newHttpClient() {
		// TODO: set auth
		return \OC::$server->getHTTPClientService()->newClient();
	}

}

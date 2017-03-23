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


namespace OCA\Market\AppStore;

use OCP\App\IAppManager;

class Service {

	public function __construct(IAppManager $appManager) {
		$this->appManager = $appManager;
	}

	/**
	 * Installs an app for the given ocs id
	 * @param integer $ocsId
	 */
	public function installApp($ocsId) {

		$info = $this->getInstalledAppInfo($ocsId);
		if (!is_null($info)) {
			throw new \InvalidArgumentException('App is already installed');
		}

		// download package
		$package = $this->downloadPackage($ocsId);
		$this->appManager->installApp($package);
	}

	private function downloadPackage($ocsId) {
		// implementation based on the current appstore - needs factory
		$ocsClient = new OCSClient(
			\OC::$server->getHTTPClientService(),
			\OC::$server->getConfig(),
			\OC::$server->getLogger()
		);
		$download = $ocsClient->getApplicationDownload($ocsId, \OCP\Util::getVersion());
		if(isset($download['downloadlink']) and $download['downloadlink'] !== '') {
			// Replace spaces in download link without encoding entire URL
			$downloadLink = str_replace(' ', '%20', $download['downloadlink']);

			$pathInfo = pathinfo($downloadLink);
			$extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
			$path = \OC::$server->getTempManager()->getTemporaryFile($extension);
			$client = \OC::$server->getHTTPClientService()->newClient();
			$client->get($downloadLink, ['save_to' => $path]);

			return $path;
		}

		throw new \Exception('Unknown app');
	}

	/**
	 * Checks if the app for the given ocs id is installed
	 *
	 * @param integer $ocsId
	 * @return bool
	 */
	public function isAppInstalled($ocsId) {
		$info = $this->getInstalledAppInfo($ocsId);
		return !is_null($info);
	}

	/**
	 * Returns the version for the app if an update is available
	 *
	 * @param integer $ocsId
	 * @return bool|string
	 */
	public function getAvailableUpdateVersion($ocsId) {
		$info = $this->getInstalledAppInfo($ocsId);
		if (is_null($info)) {
			throw new \InvalidArgumentException("App ($ocsId) is not installed");
		}
		$marketInfo = $this->getAppInfo($ocsId);
		if (is_null($marketInfo)) {
			throw new \InvalidArgumentException("App ($ocsId) is not known at the marketplace.");
		}
		$marketVersion = (string) $marketInfo['version'];
		$currentVersion = (string) $info['version'];
		if (version_compare($marketVersion, $currentVersion, '>')) {
			return $marketVersion;
		}
		return false;
	}

	private function getAppInfo($ocsId) {
		//
		// TODO: read from cached information as stored by using ./occ market:update
		//
		$ocsClient = new OCSClient(
			\OC::$server->getHTTPClientService(),
			\OC::$server->getConfig(),
			\OC::$server->getLogger()
		);
		return $ocsClient->getApplication($ocsId, \OCP\Util::getVersion());
	}

	private function getInstalledAppInfo($ocsId) {
		$apps = $this->appManager->getAllApps();
		foreach ($apps as $app) {
			$info = $this->appManager->getAppInfo($app);
			if (isset($info['ocsid']) && (int)$info['ocsid'] === (int)$ocsId) {
				return $info;
			}
		}

		return null;
	}

	/**
	 * Update the app for the given ocs id
	 *
	 * @param integer $ocsId
	 */
	public function updateApp($ocsId) {
		$info = $this->getInstalledAppInfo($ocsId);
		if (is_null($info)) {
			throw new \InvalidArgumentException('App is not installed');
		}

		// download package
		$package = $this->downloadPackage($ocsId);
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
			if (isset($info['ocsid'])) {
				try {
					$ocsId = $info['ocsid'];
					$newVersion = $this->getAvailableUpdateVersion($ocsId);
					if ($newVersion) {
						$result[$app] = [
							'version' => $newVersion,
							'ocsid' => $ocsId
						];
					}
				} catch (\InvalidArgumentException $ex) {
					// ignore exceptions thrown by getAvailableUpdateVersion
				}
			}
		}

		return $result;
	}
}

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

namespace OCA\Market\Controller;

use OCA\Market\MarketService;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class MarketController extends Controller {

	/** @var MarketService  */
	private $marketService;

	public function __construct($appName, IRequest $request, MarketService $marketService) {
		parent::__construct($appName, $request);
		$this->marketService = $marketService;
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function index() {
		// TODO: verify if app can be installed
		$apps = $this->marketService->listApps();

		return array_map(function($app) {
			$app['installed'] = $this->marketService->isAppInstalled($app['id']);
			$app['updateInfo'] = [];
			if ($app['installed']) {
				$app['installInfo'] = $this->marketService->getInstalledAppInfo($app['id']);
				$app['updateInfo'] = $this->marketService->getAvailableUpdateVersion($app['id']);
			}
			$app['releases'] = array_map(function($release) {
				$missing = $this->marketService->getMissingDependencies($release);
				$release['canInstall'] = empty($missing);
				$release['missingDependencies'] = $missing;
				return $release;
			}, $app['releases']);
			return $app;
		}, $apps);
	}

	/**
	 * @param string $appId
	 * @return array
	 */
	public function install($appId) {
		$this->marketService->installApp($appId);
		return [];
	}

	/**
	 * @param string $appId
	 * @return array
	 */
	public function update($appId) {
		$this->marketService->updateApp($appId);
		return [];
	}
}

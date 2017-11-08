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

use OCA\Market\Exception\LicenseKeyAlreadyAvailableException;
use OCA\Market\MarketService;
use OCP\App\AppManagerException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IConfig;

class MarketController extends Controller {

	/** @var MarketService */
	private $marketService;

	/** @var IL10N */
	private $l10n;

	/** @var IConfig */
	private $config;

	public function __construct($appName,
								IRequest $request,
								MarketService $marketService,
								IL10N $l10n,
								IConfig $config) {
		parent::__construct($appName, $request);
		$this->marketService = $marketService;
		$this->l10n = $l10n;
		$this->config = $config;
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function categories() {
		try {
			return $this->marketService->getCategories();
		} catch (\Exception $ex) {
			return new DataResponse(['message' => $ex->getMessage() ],
				Http::STATUS_SERVICE_UNAVAILABLE);
		}
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function bundles() {
		try {
			$bundles = $this->marketService->getBundles();
			$bundles = array_map(function ($bundle) {
				$bundle['products'] = array_map(function ($product) {
					return $this->enrichApp($product);
				}, $bundle['products']);
				return $bundle;
			}, $bundles);

			return $bundles;
		} catch (AppManagerException $ex) {
			return new DataResponse([
				'message' => $ex->getMessage()
			]);
		} catch (\Exception $ex) {
			return new DataResponse(['message' => $ex->getMessage() ],
				Http::STATUS_SERVICE_UNAVAILABLE);
		}
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function index() {
		try {
			return $this->queryData();
		} catch (AppManagerException $ex) {
			return new DataResponse([
				'message' => $ex->getMessage()
			]);
		} catch (\Exception $ex) {
			return new DataResponse(['message' => $ex->getMessage() ],
				Http::STATUS_SERVICE_UNAVAILABLE);
		}
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @param string $appId
	 * @return array|mixed
	 */
	public function app($appId) {
		try {
			$info = $this->marketService->getAppInfo($appId);
			return $this->enrichApp($info);
		} catch (\Exception $ex) {
			return new DataResponse(['message' => $ex->getMessage() ],
				Http::STATUS_SERVICE_UNAVAILABLE);
		}
	}

	/**
	 * @param string $appId
	 * @return array | DataResponse
	 */
	public function install($appId) {
		try {
			$this->marketService->installApp($appId);
			return [
				'message' => $this->l10n->t('App %s installed successfully', $appId)
			];
		} catch(\Exception $ex) {
			return new DataResponse([
				'message' => $ex->getMessage()
			], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @param $apiKey
	 * @return array|mixed
	 */
	public function changeApiKey($apiKey) {
		if (!$this->marketService->isApiKeyValid($apiKey)) {
			return new DataResponse([
					'message' => $this->l10n->t('The api key is not valid.')
				]
			);
		}
		if (!$this->marketService->setApiKey($apiKey)) {
			return new DataResponse([
					'message' => $this->l10n->t('Can not change api key because it is configured in config.php')
				]
			);
		}

		return (new Http\Response())
			->setStatus(\OC\AppFramework\Http::STATUS_OK);
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function getApiKey() {

		return new DataResponse( [
			'apiKey' => $this->marketService->getApiKey(),
			'changeable' => $this->marketService->isApiKeyChangeableByUser(),
		], Http::STATUS_OK);
	}

	/**
	 * @param string $appId
	 * @return array | DataResponse
	 */
	public function uninstall($appId) {
		try {
			$this->marketService->uninstallApp($appId);
			return [
				'message' => $this->l10n->t('App %s uninstalled successfully', $appId)
			];
		} catch(\Exception $ex) {
			return new DataResponse([
				'message' => $ex->getMessage()
			], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param string $appId
	 * @return array | DataResponse
	 */
	public function update($appId) {
		try {
			$this->marketService->updateApp($appId);
			return [
				'message' => $this->l10n->t('App %s updated successfully', $appId)
			];
		} catch(\Exception $ex) {
			return new DataResponse([
				'message' => $ex->getMessage()
			], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param string | null $category
	 * @return array
	 */
	protected function queryData($category = null) {
		$apps = $this->marketService->listApps($category);

		return array_map(function ($app) {
			return $this->enrichApp($app);
		}, $apps);
	}

	private function enrichApp($app) {
		$app['installed'] = $this->marketService->isAppInstalled($app['id']);
		$releases = array_map(function ($release) {
			$missing = $this->marketService->getMissingDependencies($release);
			$release['canInstall'] = empty($missing);
			$release['missingDependencies'] = $missing;
			return $release;
		}, $app['releases']);
		unset($app['releases']);
		if ($app['installed']) {
			$app['installInfo'] = $this->marketService->getInstalledAppInfo($app['id']);
			$app['updateInfo'] = $this->marketService->getAvailableUpdateVersion($app['id']);

			$filteredReleases = array_filter($releases, function ($release) use ($app) {
				if (empty($app['updateInfo'])) {
					return $release['version'] === $app['updateInfo'];
				}
				return $release['version'] === $app['updateInfo'];
			});
			$app['release'] = array_pop($filteredReleases);
		} else {
			$app['updateInfo'] = false;
			usort($releases, function ($a, $b) {
				return version_compare($a['version'], $b['version'], '>');
			});
			if (!empty($releases)) {
				$app['release'] = array_pop($releases);
			}
		}
		return $app;
	}

	/**
	 * @return string
	 * @NoCSRFRequired
	 */
	public function getConfig() {
		$licenseKeyAvailable = $this->marketService->hasLicenseKey();
		if ($licenseKeyAvailable) {
			$licenseMessage = $this->l10n->t('License key available.');
		} else {
			$licenseMessage = $this->l10n->t('No license key configured.');
		}

		$config = [
			'canInstall' => $this->marketService->canInstall(),
			'hasInternetConnection' => $this->config->getSystemValue('has_internet_connection', true),
			'licenseKeyAvailable' => $licenseKeyAvailable,
			'licenseMessage' => $licenseMessage
		];

		return new DataResponse($config, Http::STATUS_OK);
	}

	/**
	 * @NoCSRFRequired
	 */
	public function requestDemoLicenseKeyFromMarket() {
		try {
			$this->marketService->requestLicenseKey();
			return new DataResponse(
				[
					'message' => $this->l10n->t('Demo license key successfully fetched from the marketplace.')
				],
				Http::STATUS_OK
			);
		} catch (LicenseKeyAlreadyAvailableException $exception) {
			return new DataResponse(
				[
					'message' => $this->l10n->t('A license key is already configured.')
				],
				Http::STATUS_CONFLICT
			);
		} catch (\Exception $exception) {
			return new DataResponse(
				[
					'message' => $this->l10n->t('Could not request the license key.')
				],
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}
	}

	public function invalidateCache() {
		$this->marketService->invalidateCache();
		return new DataResponse(
			[
				'message' => $this->l10n->t('Cache cleared.')
			],
			Http::STATUS_OK
		);

	}
}

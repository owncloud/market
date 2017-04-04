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
		return $this->generateTestData();

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

	private function generateTestData() {
		$apps = [
'activity',
'admin_audit',
'comments',
'dav',
'enterprise_key',
'federatedfilesharing',
'federation',
'files',
'files_external',
'files_pdfviewer',
'files_sharing',
'files_texteditor',
'files_trashbin',
'files_versions',
'firewall',
'market',
'provisioning_api',
'systemtags',
'systemtags_management',
'templateeditor',
'updatenotification',
'workflow',
'calendar',
'clockwork',
'contacts',
'customgroups',
'documents',
'encryption',
'example-theme',
'files_antivirus',
'files_drop',
'files_external_ftp',
'files_primary_swift',
'gpxpod',
'impersonate',
'mail',
'maps',
'notes',
'oauth2',
'objectstore',
'ojsxc',
'password_policy',
'rawstorage',
'sharepoint',
'testing',
'twofactor_totp',
'user_ldap',
'user_shibboleth',
		];

		return array_map(function ($appId) {
			return [
				"id" => $appId,
				"name" => ucfirst(str_replace('_', ' ', $appId)),
				"categories" => [
					"collaboration"
				],
				"description" => "The new and improved app for your Contacts.",
				"screenshots" => [
					"url" => "https =>//marketplace.owncloud-content.com/screenshots/contacts-58e1f3a995950"
				],
				"marketplace" => "https =>//marketplace.owncloud.com/apps/contacts",
				"downloads" => 0,
				"rating" => [
					"1" => 0,
					"2" => 0,
					"3" => 0,
					"4" => 0,
					"5" => 0,
					"mean" => 0
				],
				"publisher" => [
					"name" => "ownCloud",
					"url" => "https =>//marketplace.owncloud.com/publisher/owncloud"
				],
				"releases" => [
					[
						"platformMin" => "9.0.0",
						"platformMax" => "10.0.9999",
						"version" => "1.5.1",
						"download" => "https =>//marketplace.owncloud.com/api/v1/apps/contacts/1.5.1",
						"license" => "GNU Affero General Public License",
						"created" => "2017-04-03T00 =>00 =>00+00 =>00",
						"canInstall" => true,
						"missingDependencies" => [

						]
					]
				],
				"installed" => false,
				"updateInfo" => [

				]

			];
		}, $apps);
	}
}

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
	 * @param $category
	 */
	public function appPerCategory($category) {
		return $this->queryData($category);
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function categories() {
		return json_decode('[{"id":"automation","translations":{"en":{"name":"Automation"}}},{"id":"collaboration","translations":{"en":{"name":"Collaboration"}}},{"id":"customization","translations":{"en":{"name":"Customization"}}},{"id":"external-plugins","translations":{"en":{"name":"External plugins"}}},{"id":"games","translations":{"en":{"name":"Games"}}},{"id":"integration","translations":{"en":{"name":"Integration"}}},{"id":"multimedia","translations":{"en":{"name":"Multimedia"}}},{"id":"productivity","translations":{"en":{"name":"Productivity"}}},{"id":"security","translations":{"en":{"name":"Security"}}},{"id":"storage","translations":{"en":{"name":"Storage"}}},{"id":"tools","translations":{"en":{"name":"Tools"}}}]');
		// real code below
		return $this->marketService->getCategories();
	}
	/**
	 * @NoCSRFRequired
	 *
	 * @return array|mixed
	 */
	public function index() {
		return $this->queryData();
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

	private function generateTestData($category = null) {
		$apps = [
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

		return array_map(function ($appId) use ($category) {
			if ($category === null) {
				$categories = [
					"automation",
					"collaboration",
					"customization",
					"external-plugins",
					"games",
					"integration",
					"multimedia",
					"productivity",
					"security",
					"storage",
					"tools"
				];
				$category = $categories[mt_rand(0, count($categories) - 1)];
			}
			return [
				"id" => $appId,
				"name" => ucfirst(str_replace('_', ' ', $appId)),
				"categories" => [ $category	],
				"description" => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.",
				"screenshots" => [
					"url" => "https =>//marketplace.owncloud-content.com/screenshots/contacts-58e1f3a995950"
				],
				"marketplace" => "https =>//marketplace.owncloud.com/apps/contacts",
				"downloads" => 0,
				"rating" => [
					"1" => 7,
					"2" => 0,
					"3" => 1,
					"4" => 70,
					"5" => 23,
					"mean" => 4.00990099
				],
				"publisher" => [
					"name" => "ownCloud",
					"url" => "https =>//marketplace.owncloud.com/publisher/owncloud"
				],
				"release" => [
					"platformMin" => "9.0.0",
					"platformMax" => "10.0.9999",
					"version" => "1.5.1",
					"download" => "https =>//marketplace.owncloud.com/api/v1/apps/contacts/1.5.1",
					"license" => "GNU Affero General Public License",
					"created" => 1086438197,
					"canInstall" => false,
					"missingDependencies" => [
						"PHP >= 7.0",
						"MySQL >= 5.7.17"
					]
				],
				"installed" => false,
				"updateInfo" => [

				]

			];
		}, $apps);
	}

	/**
	 * @return array
	 */
	protected function queryData($category = null) {
		return $this->generateTestData($category);

		// TODO: verify if app can be installed
		$apps = $this->marketService->listApps($category);

		return array_map(function ($app) {
			$app['installed'] = $this->marketService->isAppInstalled($app['id']);
			$app['updateInfo'] = [];
			if ($app['installed']) {
				$app['installInfo'] = $this->marketService->getInstalledAppInfo($app['id']);
				$app['updateInfo'] = $this->marketService->getAvailableUpdateVersion($app['id']);
			}
			$app['releases'] = array_map(function ($release) {
				$missing = $this->marketService->getMissingDependencies($release);
				$release['canInstall'] = empty($missing);
				$release['missingDependencies'] = $missing;
				return $release;
			}, $app['releases']);
			return $app;
		}, $apps);
	}
}

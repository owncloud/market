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

return [
	'routes' => [
		// ui controller
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		// market controller
		['name' => 'market#categories', 'url' => '/categories', 'verb' => 'GET'],
		['name' => 'market#bundles', 'url' => '/bundles', 'verb' => 'GET'],
		['name' => 'market#index', 'url' => '/apps', 'verb' => 'GET'],
		['name' => 'market#appPerCategory', 'url' => '/apps/{category}', 'verb' => 'GET'],
		['name' => 'market#install', 'url' => '/apps/{appId}/install', 'verb' => 'POST'],
		['name' => 'market#update', 'url' => '/apps/{appId}/update', 'verb' => 'POST'],
		['name' => 'market#uninstall', 'url' => '/apps/{appId}/uninstall', 'verb' => 'POST'],
		// local apps
		['name' => 'localApps#index', 'url' => '/installed-apps/{state}', 'verb' => 'GET', 'defaults' => ['state' => 'enabled']],
	],
	'resources' => []
];

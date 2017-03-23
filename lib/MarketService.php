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

use OCA\Market\Marketplace\Service;
use OCP\App\IAppManager;
use OCP\IAppConfig;
use OCP\ICacheFactory;
use OCP\IConfig;

class MarketService {

	public function __construct(IConfig $config, IAppManager $appManager, ICacheFactory $cacheFactory) {
		$storeUrl = $config->getSystemValue('appstoreurl', 'https://api.owncloud.com/v1');

		if ($storeUrl === 'https://api.owncloud.com/v1') {
			$this->impl = new AppStore\Service($appManager);
		} else {
			$this->impl = new Service($appManager, $config, $cacheFactory, $storeUrl);
		}
	}

	public function installApp($ocsId) {
		$this->impl->installApp($ocsId);
	}

	public function isAppInstalled($ocsId) {
		return $this->impl->isAppInstalled($ocsId);
	}

	public function updateAvailable($ocsId) {
		return $this->impl->getAvailableUpdateVersion($ocsId);
	}

	public function updateApp($ocsId) {
		$this->impl->updateApp($ocsId);
	}

	public function getUpdates() {
		return $this->impl->getUpdates();
	}
}

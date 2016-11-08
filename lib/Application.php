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


use OCP\AppFramework\App;

class Application extends App {

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('firewall', $urlParams);

//		$this->registerServices();
	}


	public function boot() {
		if ($this->isAdmin()) {
			\OC::$server->getNavigationManager()->add(function () {
				$urlGenerator = \OC::$server->getURLGenerator();
				$l = \OC::$server->getL10N('market');
				return [
					'id' => 'market',
					'order' => 100,
					'href' => $urlGenerator->linkToRoute('market.page.index'),
					'icon' => $urlGenerator->imagePath('market', 'market.svg'),
					'name' => $l->t('Market'),
				];
			});
		}
	}

	private function isAdmin() {
		$container = $this->getContainer();
		$userSession = $container->getServer()->getUserSession();

		$user = $userSession->getUser();
		if ($user !== null) {
			$groupManager = $container->getServer()->getGroupManager();
			return $groupManager->isAdmin($user->getUID());
		}
		return false;
	}
}

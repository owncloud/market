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

namespace OCA\Market\Command;

use OCA\Market\MarketService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeApp extends Command {

	private $marketService;

	public function __construct(MarketService $marketService) {
		parent::__construct();
		$this->marketService = $marketService;
	}

	protected function configure() {
		$this
			->setName('market:upgrade')
			->setDescription('Installs new app versions if available on the marketplace')
			->addArgument('ids',
				InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
				'Ids of the apps')
			->addOption('list')
			->addOption('all');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		if ($input->getOption('list')) {
			$updates = $this->marketService->getUpdates();
			foreach ($updates as $name => $info) {
				$output->writeln("$name : {$info['version']}");
			}
			return;
		}
		$ocsIds = $input->getArgument('ids');
		if ($input->getOption('all')) {
			$ocsIds = array_map(function($elem) {
				return $elem['ocsid'];
			}, $this->marketService->getUpdates());
		}
		$ocsIds = array_unique($ocsIds);

		foreach ($ocsIds as $ocsId) {
			try {
				if ($this->marketService->isAppInstalled($ocsId)) {
					$updateVersion = $this->marketService->updateAvailable($ocsId);
					if ($updateVersion !== false) {
						$output->writeln("$ocsId: Installing new version $updateVersion ...");
						$this->marketService->updateApp($ocsId);
						$output->writeln("$ocsId: App updated.");
					} else {
						$output->writeln("$ocsId: No update available");
					}
				} else {
					$output->writeln("$ocsId: Not installed ...");
				}
			} catch (\Exception $ex) {
				$output->writeln("$ocsId: {$ex->getMessage()}");
			}
		}
	}

}

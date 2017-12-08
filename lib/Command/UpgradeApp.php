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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeApp extends Command {

	/** @var MarketService */
	private $marketService;

	/** @var int  */
	private $exitCode = 0;

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
			->addOption('local',
				'l',
				InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
				'Optional path to a local app packages'
			)
			->addOption('list')
			->addOption('all');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		if (!$this->marketService->canInstall()) {
			throw new \Exception("Installing apps is not supported because the app folder is not writable.");
		}
		$localPackagesArray = $input->getOption('local');
		$localPackagesArray = array_unique($localPackagesArray);
		if (count($localPackagesArray)){
			foreach ($localPackagesArray as $localPackage){
				try {
					$appInfo = $this->marketService->readAppPackage($localPackage);
					$appId = $appInfo['id'];
					if ($this->marketService->isAppInstalled($appId)) {
						$installedAppInfo = $this->marketService->getInstalledAppInfo($appId);
						$currentVersion = (string) $installedAppInfo['version'];
						$packageVersion = (string) $appInfo['version'];
						if (version_compare($packageVersion, $currentVersion, '>')){
							$output->writeln("$appId: Installing new version from $localPackage");
							$this->marketService->updatePackage($localPackage);
							$output->writeln("$appId: App updated.");
						} else {
							$output->writeln("$appId: $localPackage has the same or older version of the app");
						}
					} else {
						$output->writeln("$appId: Not installed ...");
					}
				} catch (\Exception $ex) {
					$output->writeln("<error>$appId: {$ex->getMessage()}</error>");
					$this->exitCode = 1;
				}
			}
			return $this->exitCode;
		}

		if ($input->getOption('list')) {
			$updates = $this->marketService->getUpdates();
			foreach ($updates as $name => $info) {
				$output->writeln("$name : {$info['version']}");
			}
			return;
		}
		$appIds = $input->getArgument('ids');
		if ($input->getOption('all')) {
			$appIds = array_map(function($elem) {
				return $elem['ocsid'];
			}, $this->marketService->getUpdates());
		}
		$appIds = array_unique($appIds);

		if (!count($appIds)){
			$output->writeln("No appId or path to a local package specified. Nothing to do.");
			return;
		}

		foreach ($appIds as $appId) {
			try {
				if ($this->marketService->isAppInstalled($appId)) {
					$updateVersion = $this->marketService->getAvailableUpdateVersion($appId);
					if ($updateVersion !== false) {
						$output->writeln("$appId: Installing new version $updateVersion ...");
						$this->marketService->updateApp($appId);
						$output->writeln("$appId: App updated.");
					} else {
						$output->writeln("$appId: No update available");
					}
				} else {
					$output->writeln("$appId: Not installed ...");
				}
			} catch (\Exception $ex) {
				$output->writeln("<error>$appId: {$ex->getMessage()}</error>");
				$this->exitCode = 1;
			}
		}
		return $this->exitCode;
	}
}

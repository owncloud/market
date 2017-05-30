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

use OC\IntegrityCheck\Checker;
use OCA\Market\MarketService;
use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCrl extends Command {

	/** @var MarketService */
	private $marketService;
	/** @var IConfig */
	private $config;
	/** @var Checker */
	private $checker;

	public function __construct(MarketService $marketService, IConfig $config, Checker $checker) {
		parent::__construct();
		$this->marketService = $marketService;
		$this->config = $config;
		$this->checker = $checker;
	}

	protected function configure() {
		$this
			->setName('market:update-crl')
			->setDescription('Downloads the current certificate revocation list from the marketplace');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln("Downloading certificate revocation list ...");
		$crl = $this->marketService->downloadCrl();
		$output->writeln("Validating certificate revocation list ...");
		$this->checker->validateCrl($crl);
		$output->writeln("Writing intermediate.crl.pem ...");
		$target = $this->config->getSystemValue('datadirectory') . '/intermediate.crl.pem';
		file_put_contents($target, $crl);
		$output->writeln("Done.");
	}
}

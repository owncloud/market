<?php

/**
 * @author Ilja Neumann <ineumann@owncloud.com>
 *
 * @copyright Copyright (c) 2019, ownCloud GmbH
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
namespace OCA\Market\Tests\Unit;

use OCA\Market\Controller\MarketController;
use OCA\Market\MarketService;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use Test\TestCase;

class MarketControllerTest extends TestCase {

	/** @var MarketController */
	private $controller;
	/** @var IRequest */
	private $request;
	/** @var MarketService|\PHPUnit_Framework_MockObject_MockObject */
	private $marketService;

	public function setUp() {
		parent::setUp();
		$this->request = $this->createMock(IRequest::class);
		$this->marketService = $this->createMock(MarketService::class);

		$this->controller = new MarketController(
			'market',
			$this->request,
			$this->marketService,
			$this->createMock(IL10N::class),
			$this->createMock(IURLGenerator::class),
			$this->createMock(IConfig::class)
		);
	}

	public function testApiKeyIsNotReturnedIfNotChangeableByUser() {
		$this->marketService
			->method('isApiKeyChangeableByUser')
			->willReturn(false);

		/** @var array */
		$response = $this->controller->getApiKey()->getData();
		$this->assertArrayNotHasKey('apiKey', $response);
	}

	public function testApiKeyIsReturnedIfChangeableByUser() {
		$this->marketService
			->method('isApiKeyChangeableByUser')
			->willReturn(true);

		/** @var array */
		$response = $this->controller->getApiKey()->getData();
		$this->assertArrayHasKey('apiKey', $response);
	}
}

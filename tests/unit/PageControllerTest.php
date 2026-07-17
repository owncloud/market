<?php

namespace OCA\Market\Tests\Unit;

use OCA\Market\Controller\PageController;
use OCP\IConfig;
use OCP\IRequest;
use Test\TestCase;

class PageControllerTest extends TestCase {
	private $appName = 'market';
	/** @var IRequest */
	private $request;
	/** @var IConfig */
	private $config;
	/** @var PageController */
	private $controller;

	protected function setUp(): void {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->config = $this->createMock(IConfig::class);
		$this->config->method('getSystemValue')
			->with('appstoreurl', 'https://marketplace.owncloud.com')
			->willReturn('https://my.appstore.example/some/path');
		$this->controller = new PageController($this->appName, $this->request, $this->config);
	}

	private function expectedPolicy() {
		$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$policy->addAllowedImageDomain('https://marketplace-storage.owncloud.com');
		$policy->addAllowedImageDomain('https://marketplace-storage.staging.owncloud.services');
		$policy->addAllowedImageDomain('http://minio:9000');
		$policy->addAllowedImageDomain('https://my.appstore.example');
		return $policy;
	}

	public function testIndex() {
		$response = $this->controller->index();

		$this->assertEquals($this->expectedPolicy(), $response->getContentSecurityPolicy());

		$this->assertEquals('index', $response->getTemplateName());
	}

	public function testIndexHash() {
		$response = $this->controller->indexHash();

		$this->assertEquals($this->expectedPolicy(), $response->getContentSecurityPolicy());

		$this->assertEquals('index', $response->getTemplateName());
	}
}

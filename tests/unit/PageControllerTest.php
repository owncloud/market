<?php

namespace OCA\Market\Tests\Unit;

use OCA\Market\Controller\PageController;
use OCP\IRequest;
use Test\TestCase;

class PageControllerTest extends TestCase {
	private $appName = 'market';
	/** @var IRequest */
	private $request;
	/** @var PageController */
	private $controller;

	protected function setUp(): void {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->controller = new PageController($this->appName, $this->request);
	}

	public function testIndex() {
		$response = $this->controller->index();

		$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$policy->addAllowedImageDomain('https://marketplace-storage.owncloud.com');
		$policy->addAllowedImageDomain('https://marketplace-storage.staging.owncloud.services');
		$policy->addAllowedImageDomain('http://minio:9000');
		$this->assertEquals($policy, $response->getContentSecurityPolicy());

		$this->assertEquals('index', $response->getTemplateName());
	}

	public function testIndexHash() {
		$response = $this->controller->indexHash();

		$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$policy->addAllowedImageDomain('https://marketplace-storage.owncloud.com');
		$policy->addAllowedImageDomain('https://marketplace-storage.staging.owncloud.services');
		$policy->addAllowedImageDomain('http://minio:9000');
		$this->assertEquals($policy, $response->getContentSecurityPolicy());

		$this->assertEquals('index', $response->getTemplateName());
	}
}

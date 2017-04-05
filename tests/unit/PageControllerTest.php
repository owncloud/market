<?php

use OCA\Market\Controller\PageController;
use OCP\IRequest;
use Test\TestCase;

class PageControllerTest extends TestCase {

	private $appName = 'market';
	/** @var IRequest */
	private $request;
	/** @var PageController */
	private $controller;

	protected function setUp() {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->controller = new PageController($this->appName, $this->request);
	}

	public function testIndex() {

		$response = $this->controller->index();

		$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$policy->addAllowedImageDomain('https://storage.marketplace.owncloud.com');
		$policy->addAllowedImageDomain('https://unsplash.it');
		$this->assertEquals($policy, $response->getContentSecurityPolicy());

		$this->assertEquals('index', $response->getTemplateName());
	}

}

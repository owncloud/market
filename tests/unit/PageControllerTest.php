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

		$expected = new \OCP\AppFramework\Http\TemplateResponse($this->appName, 'index', []);
		$response = $this->controller->index();

		$this->assertEquals($expected, $response);
	}

}

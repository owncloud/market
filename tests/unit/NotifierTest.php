<?php

namespace OCA\Market\Tests\Unit;

use OC\Notification\Notification;
use OCA\Market\Notifier;
use OCP\App\IAppManager;
use OCP\L10N\IFactory;
use OCP\IL10N;
use OCP\Notification\IManager;
use Test\TestCase;

class NotifierTest extends TestCase {
	protected $appManager;

	protected $notificationManager;

	protected $l10nFactory;

	protected function setUp(): void {
		parent::setUp();
		$l10n = $this->getMockBuilder(IL10N::class)
			->disableOriginalConstructor()
			->getMock();

		$l10n->expects($this->any())
			->method('t')
			->willReturnArgument(0);

		$this->l10nFactory = $this->getMockBuilder(IFactory::class)
			->disableOriginalConstructor()
			->getMock();

		$this->l10nFactory->expects($this->any())
			->method('get')
			->willReturn($l10n);

		$this->notificationManager = $this->getMockBuilder(IManager::class)
			->disableOriginalConstructor()
			->getMock();

		$this->appManager = $this->getMockBuilder(IAppManager::class)
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 */
	public function testUnexistingAppsDoNotCreateNotifications() {
		$this->expectException(\InvalidArgumentException::class);

		$appName = 'whatsapp';
		$notifier = new Notifier($this->notificationManager, $this->appManager, $this->l10nFactory);
		$notification = new Notification();
		$notification->setApp('market');
		$notification->setObject($appName, 55);
		$notification->setSubject('Cu');
		$notifier->prepare($notification, 'en');
	}

	/**
	 */
	public function testUninstalledAppsDoNotCreateNotifications() {
		$this->expectException(\InvalidArgumentException::class);

		$appName = 'whatsapp';
		$notifier = $this->getMockBuilder(Notifier::class)
			->setConstructorArgs([$this->notificationManager, $this->appManager, $this->l10nFactory])
			->setMethods(['getAppVersions'])
			->getMock();

		$notifier->expects($this->any())
			->method('getAppVersions')
			->willReturn([$appName => '1.2.4']);

		$this->appManager->expects($this->any())
			->method('getAppPath')
			->with($appName)
			->willReturn(false);

		$notification = new Notification();
		$notification->setApp('market');
		$notification->setObject($appName, 55);
		$notification->setSubject('Cu');
		$notifier->prepare($notification, 'en');
	}
}

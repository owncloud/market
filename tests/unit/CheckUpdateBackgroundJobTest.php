<?php

namespace OCA\Market\Tests\Notification;

use OCA\Market\CheckUpdateBackgroundJob;
use OCA\Market\MarketService;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IConfig;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Notification\IManager;
use OCP\Notification\INotification;
use Test\TestCase;

class CheckUpdateBackgroundJobTest extends TestCase {

	/** @var IConfig|\PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var ITimeFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $timeFactory;
	/** @var IManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $notificationManager;
	/** @var IGroupManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $groupManager;
	/** @var MarketService |\PHPUnit_Framework_MockObject_MockObject */
	private $marketService;
	/** @var IURLGenerator|\PHPUnit_Framework_MockObject_MockObject */
	protected $urlGenerator;


	public function setUp() {
		parent::setUp();

		$this->config = $this->createMock(IConfig::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);
		$this->notificationManager = $this->createMock(IManager::class);
		$this->groupManager = $this->createMock(IGroupManager::class);
		$this->marketService = $this->createMock(MarketService::class);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
	}

	/**
	 * @param array $methods
	 * @return CheckUpdateBackgroundJob|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getJob(array $methods = []) {
		if (empty($methods)) {
			return new CheckUpdateBackgroundJob(
				$this->config,
				$this->timeFactory,
				$this->notificationManager,
				$this->groupManager,
				$this->marketService,
				$this->urlGenerator
			);
		} {
			return $this->getMockBuilder(CheckUpdateBackgroundJob::class)
				->setConstructorArgs([
					$this->config,
					$this->timeFactory,
					$this->notificationManager,
					$this->groupManager,
					$this->marketService,
					$this->urlGenerator,
				])
				->setMethods($methods)
				->getMock();
		}
	}

	public function dataCheckAppUpdate() {
		return [
			[
				['test'=> ['id' => 'test', 'version' => '1.2.4']],
				true,
			],
			[
				[],
				false,
			],
		];
	}

	/**
	 * @dataProvider dataCheckAppUpdate
	 *
	 * @param array $marketApps
	 * @param bool $shouldNotify
	 */
	public function testCheckAppUpdate($marketApps, $shouldNotify) {
		$job = $this->getJob([
			'createNotifications',
		]);

		$this->marketService->expects($this->once())
			->method('getUpdates')
			->willReturn($marketApps);

		$this->timeFactory->expects($this->any())
			->method('getTime')
			->willReturn(42);

		if ($shouldNotify) {
			$this->urlGenerator->expects($this->once())
				->method('linkToRouteAbsolute')
				->with('market.page.index')
				->willReturn('meow');

			$job->expects($this->once())
				->method('createNotifications')
				->willReturn(null);
		} else {
			$this->urlGenerator->expects($this->never())
				->method('linkToRouteAbsolute');

			$job->expects($this->never())
				->method('createNotifications');
		}

		$this->invokePrivate($job, 'run', [null]);
	}

	public function dataCreateNotifications() {
		return [
			['app1', '1.0.0', 'link1', '1.0.0', false, false, null, null],
			['app2', '1.0.1', 'link2', '1.0.0', '1.0.0', true, ['user1'], [['user1']]],
			['app3', '1.0.1', 'link3', false, false, true, ['user2', 'user3'], [['user2'], ['user3']]],
		];
	}

	/**
	 * @dataProvider dataCreateNotifications
	 *
	 * @param string $app
	 * @param string $version
	 * @param string $url
	 * @param string|false $lastNotification
	 * @param string|false $callDelete
	 * @param bool $createNotification
	 * @param string[]|null $users
	 * @param array|null $userNotifications
	 */
	public function testCreateNotifications($app, $version, $url, $lastNotification, $callDelete, $createNotification, $users, $userNotifications) {
		$job = $this->getJob([
			'deleteOutdatedNotifications',
			'getUsersToNotify',
		]);

		$this->timeFactory->expects($this->any())
			->method('getTime')
			->willReturn(42);

		$this->config->expects($this->once())
			->method('getAppValue')
			->with('market', $app, false)
			->willReturn($lastNotification);

		if ($lastNotification !== $version) {
			$this->config->expects($this->once())
				->method('setAppValue')
				->with('market', $app, $version);
		}

		if ($callDelete === false) {
			$job->expects($this->never())
				->method('deleteOutdatedNotifications');
		} else {
			$job->expects($this->once())
				->method('deleteOutdatedNotifications')
				->with($app, $callDelete);
		}

		if ($users === null) {
			$job->expects($this->never())
				->method('getUsersToNotify');
		} else {
			$job->expects($this->once())
				->method('getUsersToNotify')
				->willReturn($users);
		}

		if ($createNotification) {
			$notification = $this->createMock(INotification::class);
			$notification->expects($this->once())
				->method('setApp')
				->with('market')
				->willReturnSelf();
			$notification->expects($this->once())
				->method('setDateTime')
				->willReturnSelf();
			$notification->expects($this->once())
				->method('setObject')
				->with($app, $version)
				->willReturnSelf();
			$notification->expects($this->once())
				->method('setSubject')
				->with('update_available')
				->willReturnSelf();
			$notification->expects($this->once())
				->method('setLink')
				->with($url)
				->willReturnSelf();

			if ($userNotifications !== null) {
				$mockedMethod = $notification->expects($this->exactly(sizeof($userNotifications)))
					->method('setUser')
					->willReturnSelf();
				call_user_func_array([$mockedMethod, 'withConsecutive'], $userNotifications);

				$this->notificationManager->expects($this->exactly(sizeof($userNotifications)))
					->method('notify')
					->willReturn($notification);
			}

			$this->notificationManager->expects($this->once())
				->method('createNotification')
				->willReturn($notification);
		} else {
			$this->notificationManager->expects($this->never())
				->method('createNotification');
		}

		$this->invokePrivate($job, 'createNotifications', [$app, $version, $url]);
	}

	public function dataGetUsersToNotify() {
		return [
			[['g1', 'g2'], ['g1' => null, 'g2' => ['u1', 'u2']], ['u1', 'u2']],
			[['g3', 'g4'], ['g3' => ['u1', 'u2'], 'g4' => ['u2', 'u3']], ['u1', 'u2', 'u3']],
		];
	}

	/**
	 * @dataProvider dataGetUsersToNotify
	 * @param string[] $groups
	 * @param array $groupUsers
	 * @param string[] $expected
	 */
	public function testGetUsersToNotify($groups, array $groupUsers, array $expected) {
		$job = $this->getJob();

		$this->config->expects($this->once())
			->method('getAppValue')
			->with('market', 'notify_groups', '["admin"]')
			->willReturn(json_encode($groups));

		$groupMap = [];
		foreach ($groupUsers as $gid => $uids) {
			if ($uids === null) {
				$group = null;
			} else {
				$group = $this->getGroup($gid);
				$group->expects($this->any())
					->method('getUsers')
					->willReturn($this->getUsers($uids));
			}
			$groupMap[] = [$gid, $group];
		}
		$this->groupManager->expects($this->exactly(sizeof($groups)))
			->method('get')
			->willReturnMap($groupMap);

		$result = $this->invokePrivate($job, 'getUsersToNotify');
		$this->assertEquals($expected, $result);

		// Test caching
		$result = $this->invokePrivate($job, 'getUsersToNotify');
		$this->assertEquals($expected, $result);
	}

	public function dataDeleteOutdatedNotifications() {
		return [
			['app1', '1.1.0'],
			['app2', '1.2.0'],
		];
	}

	/**
	 * @dataProvider dataDeleteOutdatedNotifications
	 * @param string $app
	 * @param string $version
	 */
	public function testDeleteOutdatedNotifications($app, $version) {
		$notification = $this->createMock(INotification::class);
		$notification->expects($this->once())
			->method('setApp')
			->with('market')
			->willReturnSelf();
		$notification->expects($this->once())
			->method('setObject')
			->with($app, $version)
			->willReturnSelf();

		$this->notificationManager->expects($this->once())
			->method('createNotification')
			->willReturn($notification);
		$this->notificationManager->expects($this->once())
			->method('markProcessed')
			->with($notification);

		$job = $this->getJob();
		$this->invokePrivate($job, 'deleteOutdatedNotifications', [$app, $version]);
	}

	/**
	 * @param string[] $userIds
	 * @return IUser[]|\PHPUnit_Framework_MockObject_MockObject[]
	 */
	protected function getUsers(array $userIds) {
		$users = [];
		foreach ($userIds as $uid) {
			$user = $this->createMock(IUser::class);
			$user->expects($this->any())
				->method('getUID')
				->willReturn($uid);
			$users[] = $user;
		}
		return $users;
	}

	/**
	 * @param string $gid
	 * @return \OCP\IGroup|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getGroup($gid) {
		$group = $this->createMock(IGroup::class);
		$group->expects($this->any())
			->method('getGID')
			->willReturn($gid);
		return $group;
	}
}

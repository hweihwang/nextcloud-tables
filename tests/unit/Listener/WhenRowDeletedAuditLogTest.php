<?php

namespace unit\Listener;

use OCA\Tables\Db\Row2;
use OCA\Tables\Event\RowDeletedEvent;
use OCA\Tables\Listener\WhenRowDeletedAuditLogListener;
use OCA\Tables\Service\Support\AuditLogServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class WhenRowDeletedAuditLogTest extends TestCase {
	private WhenRowDeletedAuditLogListener $listener;
	private MockObject $auditLogService;

	protected function setUp(): void {
		$this->auditLogService = $this->createMock(AuditLogServiceInterface::class);

		$this->listener = new WhenRowDeletedAuditLogListener($this->auditLogService);
	}

	public function testHandle(): void {
		$row = new Row2();
		$row->setId(1);
		$userId = 'user1';

		$event = new RowDeletedEvent($row, $userId);

		$this->auditLogService
			->expects($this->once())
			->method('log')
			->with(
				$this->equalTo("Row with ID: {$row->getId()} was deleted by user with ID: $userId"),
				$this->equalTo([
					'row' => $row->jsonSerialize(),
					'userId' => $userId,
				])
			);

		$this->listener->handle($event);
	}
}

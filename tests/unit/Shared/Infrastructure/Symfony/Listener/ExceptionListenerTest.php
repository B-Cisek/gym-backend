<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Symfony\Listener;

use App\Shared\Domain\DomainException;
use App\Shared\Domain\NotFoundException;
use App\Shared\Infrastructure\Symfony\Listener\ExceptionListener;
use App\Shared\Infrastructure\Symfony\Response\ValidationErrorMapper;
use App\Shared\Infrastructure\Symfony\Response\ValidationErrorResponseDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * @internal
 *
 * @coversNothing
 */
final class ExceptionListenerTest extends TestCase
{
    private MockObject&ValidationErrorMapper $validationMapper;

    protected function setUp(): void
    {
        $this->validationMapper = $this->createMock(ValidationErrorMapper::class);
    }

    #[Test]
    public function it_handles_validation_exception_with_422_status(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');

        $violations = new ConstraintViolationList([
            new ConstraintViolation('Email is required', '', [], '', 'email', ''),
            new ConstraintViolation('Password is too short', '', [], '', 'password', ''),
        ]);

        $validationException = new ValidationFailedException('value', $violations);
        $httpException = new HttpException(422, 'Validation failed', $validationException);

        $dto = new ValidationErrorResponseDTO(
            message: 'Validation failed',
            errors: [
                'email' => ['Email is required'],
                'password' => ['Password is too short'],
            ],
        );

        $this->validationMapper
            ->expects($this->once())
            ->method('map')
            ->with($violations)
            ->willReturn($dto)
        ;

        $event = $this->createExceptionEvent($httpException);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(422, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame('Validation failed', $content['message']);
        $this->assertArrayHasKey('errors', $content);
        $this->assertSame(['Email is required'], $content['errors']['email']);
        $this->assertSame(['Password is too short'], $content['errors']['password']);
    }

    #[Test]
    public function it_handles_domain_exception_with_custom_status_code(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');
        $exception = new class('User already exists') extends DomainException {
            public function getHttpStatusCode(): int
            {
                return 409;
            }
        };

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(409, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame('User already exists', $content['message']);
        $this->assertArrayNotHasKey('trace', $content);
    }

    #[Test]
    public function it_includes_stack_trace_for_domain_exception_in_dev_environment(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'dev');
        $exception = new class('Domain error') extends DomainException {
            public function getHttpStatusCode(): int
            {
                return 400;
            }
        };

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('trace', $content);
        $this->assertArrayHasKey('file', $content['trace']);
        $this->assertArrayHasKey('line', $content['trace']);
        $this->assertArrayHasKey('trace', $content['trace']);
        $this->assertIsString($content['trace']['file']);
        $this->assertIsInt($content['trace']['line']);
        $this->assertIsString($content['trace']['trace']);
    }

    #[Test]
    public function it_handles_not_found_exception_with_404_status(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');
        $exception = new NotFoundException('User not found');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(404, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame('User not found', $content['message']);
        $this->assertArrayNotHasKey('trace', $content);
    }

    #[Test]
    public function it_includes_stack_trace_for_not_found_exception_in_dev_environment(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'dev');
        $exception = new NotFoundException('Resource not found');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('trace', $content);
        $this->assertArrayHasKey('file', $content['trace']);
        $this->assertArrayHasKey('line', $content['trace']);
        $this->assertArrayHasKey('trace', $content['trace']);
    }

    #[Test]
    public function it_handles_generic_exception_with_500_status_in_prod(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');
        $exception = new \RuntimeException('Database connection failed');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame('Internal server error', $content['message']);
        $this->assertArrayNotHasKey('trace', $content);
    }

    #[Test]
    public function it_shows_actual_message_for_generic_exception_in_dev(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'dev');
        $exception = new \RuntimeException('Database connection failed');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame('Database connection failed', $content['message']);
        $this->assertArrayHasKey('trace', $content);
    }

    #[Test]
    public function it_does_not_treat_http_exception_without_validation_previous_as_validation_error(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');
        $exception = new HttpException(400, 'Bad request');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $this->assertSame(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame('Internal server error', $content['message']);
    }

    #[Test]
    public function it_validation_mapper_is_not_called_for_non_validation_exceptions(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');
        $exception = new NotFoundException('Not found');
        $event = $this->createExceptionEvent($exception);

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        // When
        $listener->onKernelException($event);

        // Then
        $this->assertNotNull($event->getResponse());
    }

    #[Test]
    public function it_handles_multiple_validation_violations_for_same_field(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'prod');

        $violations = new ConstraintViolationList([
            new ConstraintViolation('Too short', '', [], '', 'password', ''),
            new ConstraintViolation('Must contain number', '', [], '', 'password', ''),
        ]);

        $validationException = new ValidationFailedException('value', $violations);
        $httpException = new HttpException(422, 'Validation failed', $validationException);

        $dto = new ValidationErrorResponseDTO(
            message: 'Validation failed',
            errors: [
                'password' => ['Too short', 'Must contain number'],
            ],
        );

        $this->validationMapper
            ->expects($this->once())
            ->method('map')
            ->with($violations)
            ->willReturn($dto)
        ;

        $event = $this->createExceptionEvent($httpException);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertCount(2, $content['errors']['password']);
        $this->assertContains('Too short', $content['errors']['password']);
        $this->assertContains('Must contain number', $content['errors']['password']);
    }

    #[Test]
    public function it_stack_trace_contains_expected_fields(): void
    {
        // Given
        $listener = new ExceptionListener($this->validationMapper, 'dev');
        $exception = new \RuntimeException('Test exception');

        $this->validationMapper
            ->expects($this->never())
            ->method('map')
        ;

        $event = $this->createExceptionEvent($exception);

        // When
        $listener->onKernelException($event);

        // Then
        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('trace', $content);
        $trace = $content['trace'];

        $this->assertArrayHasKey('file', $trace);
        $this->assertArrayHasKey('line', $trace);
        $this->assertArrayHasKey('trace', $trace);

        $this->assertIsString($trace['file']);
        $this->assertIsInt($trace['line']);
        $this->assertIsString($trace['trace']);
        $this->assertNotEmpty($trace['file']);
        $this->assertGreaterThan(0, $trace['line']);
    }

    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = new Request();

        return new ExceptionEvent(
            kernel: $kernel,
            request: $request,
            requestType: HttpKernelInterface::MAIN_REQUEST,
            e: $exception
        );
    }
}

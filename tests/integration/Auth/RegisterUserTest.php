<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth;

use App\Auth\Domain\Email;
use App\Auth\Domain\UserRepository;
use App\Owner\Domain\OwnerRepository;
use App\Shared\Application\Message\SendEmailMessage;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterUserTest extends WebTestCase
{
    private const string OWNER_REGISTER_PATH = '/api/v1/auth/owner/register';
    private const string MEMBER_REGISTER_PATH = '/api/v1/auth/member/register';

    protected function setUp(): void
    {
        self::createClient();
        $this->getAsyncTransport()->reset();
    }

    #[Test]
    public function it_registers_owner_and_returns_auth_tokens(): void
    {
        $client = $this->getClient();
        $email = sprintf('member-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());
        self::assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refresh_token', $data);
        self::assertNotEmpty($data['token']);
        self::assertNotEmpty($data['refresh_token']);
    }

    #[Test]
    public function it_creates_owner_for_registered_user(): void
    {
        $client = $this->getClient();
        $email = sprintf('owner-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        /** @var OwnerRepository $ownerRepository */
        $ownerRepository = self::getContainer()->get(OwnerRepository::class);

        $user = $userRepository->getByEmail(Email::fromString($email));
        $owner = $ownerRepository->getByUserId($user->getId());

        self::assertSame($user->getId()->toString(), $owner->userId->toString());
        self::assertFalse($owner->isProfileComplete);
    }

    #[Test]
    public function it_dispatches_welcome_email_message_after_registration(): void
    {
        $client = $this->getClient();
        $email = sprintf('welcome-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());

        $sentMessages = array_map(
            static fn ($envelope) => $envelope->getMessage(),
            $this->getAsyncTransport()->getSent()
        );

        $welcomeMessages = array_values(array_filter(
            $sentMessages,
            static fn ($message) => $message instanceof SendEmailMessage
                && $message->recipient === $email
                && $message->subject === 'Welcome to Gym Management!'
        ));

        self::assertNotEmpty($welcomeMessages, 'Expected welcome email message to be dispatched.');
    }

    #[Test]
    public function it_returns_conflict_for_duplicate_owner_registration(): void
    {
        $client = $this->getClient();
        $email = sprintf('duplicate-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        /** @var OwnerRepository $ownerRepository */
        $ownerRepository = self::getContainer()->get(OwnerRepository::class);
        $userAfterFirstRegistration = $userRepository->getByEmail(Email::fromString($email));
        $ownerAfterFirstRegistration = $ownerRepository->getByUserId($userAfterFirstRegistration->getId());

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT, $client->getResponse()->getContent());
        self::assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('message', $data);
        self::assertStringContainsString($email, $data['message']);

        $userAfterSecondRegistration = $userRepository->getByEmail(Email::fromString($email));
        $ownerAfterSecondRegistration = $ownerRepository->getByUserId($userAfterSecondRegistration->getId());

        self::assertSame($userAfterFirstRegistration->getId()->toString(), $userAfterSecondRegistration->getId()->toString());
        self::assertSame($ownerAfterFirstRegistration->id->toString(), $ownerAfterSecondRegistration->id->toString());
    }

    #[Test]
    public function it_assigns_owner_role_for_owner_registration(): void
    {
        $client = $this->getClient();
        $email = sprintf('role-owner-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::OWNER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->getByEmail(Email::fromString($email));

        self::assertSame(['ROLE_OWNER'], $user->getRoles());
    }

    #[Test]
    public function it_assigns_member_role_for_member_registration(): void
    {
        $client = $this->getClient();
        $email = sprintf('role-member-%s@example.com', bin2hex(random_bytes(8)));

        $client->jsonRequest('POST', self::MEMBER_REGISTER_PATH, [
            'email' => $email,
            'password' => 'password',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getContent());

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->getByEmail(Email::fromString($email));

        self::assertSame(['ROLE_MEMBER'], $user->getRoles());
    }

    private function getAsyncTransport(): InMemoryTransport
    {
        /** @var InMemoryTransport $transport */
        return self::getContainer()->get('messenger.transport.async');
    }
}

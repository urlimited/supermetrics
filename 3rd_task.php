<?php
/**
 * Use case devoted for the business logic scenario
 * when user is received via validated email
 */
final class GetUserUseCase extends UseCase {

    /**
     * Used to provide one more layer between business application layer
     * and data layer via application layer that is depending only on the
     * interface
     * @type UsersRepository
     */
    private UsersRepository $usersRepository;

    // Via dependency injection we will receive this from ApplicationContainer
    public function __construct(UsersRepository $usersRepository){
        $this->usersRepository = $usersRepository;
    }

    public function process(array $validateData): UseCaseResult
    {
        $user = $this->usersRepository->getUser($validateData['email']);

        if(is_null($user)) {
            UseCaseResult::fail(
                content: 'User is not found with the email',
                scenario: 'user.not_found'
            );
        }

        return UseCaseResult::success($user);
    }
}

abstract class UseCase {
    abstract public function process(array $validatedData): UseCaseResult;
}

final class UseCaseResult {
    public mixed $content;

    public static function success(mixed $content): self
    {
        $result = new UseCaseResult();

        $result->content = $content;

        return $result;
    }

    /**
     * @param mixed $content
     * @throws UseCaseExceptionScenario
     * @return void
     */
    public static function fail(mixed $content, string $scenario): void
    {
        throw new UseCaseExceptionScenario(
            scenario: $scenario,
            context: $content
        );
    }
}

// UsersRepository realization will use factories with builders
// in order to encapsulate difficult logic for entity creation
interface UsersRepository {
    public function getUserByEmail(string $email): UserEntity|null;
}

// According to DDD, user model is required to be
// unique and continuous during the life cycle of the model
// we will assume that email is a unique filed for the model,
// by which we can identify it (we remember about id, but id does
// not provide meaning in terms of business logic in current situation)
final class UserEntity extends EntityModel {
    public function __construct(
        private string $email,
        private string $username
    ){

    }

    public function getUserName(): string
    {
        return $this->username;
    }
}


final class UseCaseExceptionScenario extends Exception {
    public function __construct(
        public string $scenario,
        public ?string $context,
        public string $message = "",
        public int $code = 0,
        public ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

final class UserPublicResource implements PublishableResource {
    #[ArrayShape(['username' => "string"])]
    public static function publish($entity): array
    {
        return [
            'username' => $entity->getUserName()
        ];
    }
}

interface PublishableResource {
    public static function publish($entity): array;
}
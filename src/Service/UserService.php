<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Validation\Validator;
use App\Models\User;
use App\Models\UserDto;
use App\Repository\UserRepository;

final readonly class UserService
{
    public function __construct(
        private Validator $validator,
        private UserRepository $userRepository
    ) {
    }

    public function create(array $payload): array
    {
        $response = [
            'errors' => [],
            'user'   => [],
            'status' => true
        ];
        $input  = UserDto::fromArray($payload);
        $errors = $this->validator->validate($input);

        if (!empty($errors)) {
            $response['status'] = false;
            $response['errors'] = $errors;
            return $response;
        }
        $hash = password_hash($input->password, PASSWORD_DEFAULT);
        if ($this->userRepository->findByEmail($input->email)) {
            $response['status'] = false;
            $response['errors'] = ['message' => 'User already exists'];
            return $response;
        }
        $user             = User::create($input->email, $hash);
        $id               = $this->userRepository->insert($user);
        $response['user'] = ['id' => $id, 'email' => $input->email];
        return $response;
    }
}

<?php
declare(strict_types=1);

namespace App;

use App\Controller\AuthController;
use App\Controller\UserController;
use App\Core\Auth;
use App\Core\Autowire;
use App\Core\Database;
use App\Core\Router;
use App\Core\Validation\ConstraintValidatorRegistry;
use App\Core\Validation\Validator;
use App\Core\Validation\Validators\ChoiceValidator;
use App\Core\Validation\Validators\EmailValidator;
use App\Core\Validation\Validators\KeysChoiceValidator;
use App\Core\Validation\Validators\LengthValidator;
use App\Core\Validation\Validators\NotBlankValidator;
use App\Repository\UserRepository;
use App\Service\UserService;
use PDO;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel
{
    public static function load(string $baseDir): void
    {
        if (is_file($baseDir . '/.env')) {
            (new Dotenv())->loadEnv($baseDir . '/.env');
        }

        $database = Database::connectDatabase();
        Database::migrate($database, $baseDir . '/migrations/initDatabase.sql');
        $request = Request::createFromGlobals();

        $registry = new ConstraintValidatorRegistry([
            new NotBlankValidator(),
            new EmailValidator(),
            new LengthValidator(),
            new KeysChoiceValidator(),
            new ChoiceValidator(),
        ]);
        $validator = new Validator($registry);

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
        if ($request->getPathInfo() === '/auth/login' && $request->isMethod('POST')) {
            $response = (new AuthController($database))->login($request);
            self::send($response);
            return;
        }
        if ($request->getPathInfo() === '/users' && $request->isMethod('POST')) {
            $service = new UserService($validator, new UserRepository($database));
            $response = (new UserController($request, $service))->create();
            self::send($response);
            return;
        }

        $router = new Router($baseDir . '/src/Controller', 'App\\Controller');

        $autowire = new Autowire([
            PDO::class => $database,
            Request::class => $request,
            Validator::class => $validator,
        ]);

        $controllers = static fn(string $controller) => $autowire->make($controller);
        $authUser = Auth::requireBasic($request, $database);
        $request->attributes->set('auth_user', $authUser);
        try {
            $response = $router->dispatch($request, $controllers);
            self::send($response);
        } catch (\Throwable $e) {
            self::send(new JsonResponse([
                'error'=>['code'=>'INTERNAL','message'=>$e->getMessage()]
            ], 500));
        }
    }

    private static function send(Response $response): void
    {
        if (!$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        }
        $response->send();
    }
}

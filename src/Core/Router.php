<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Attributes\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Router
{
    private array $routes = [];

    public function __construct(
        private readonly string $dir,
        private readonly string $namespace = 'App\\Controller'
    ) {
        foreach (glob($this->dir . '/*.php') ?: [] as $file) {
            require_once $file;

            $class = $this->namespace . '\\' . basename($file, '.php');
            if (!class_exists($class)) {
                continue;
            }

            $refClass = new \ReflectionClass($class);
            if (!$refClass->isInstantiable()) {
                continue;
            }

            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                foreach ($method->getAttributes(Route::class) as $attribute) {
                    /** @var Route $route */
                    $route          = $attribute->newInstance();
                    [$regex, $vars] = $this->compile($route->path);

                    $this->routes[] = [
                        'regex'   => $regex,
                        'vars'    => $vars,
                        'methods' => array_map('strtoupper', $route->methods),
                        'class'   => $class,
                        'method'  => $method->getName(),
                    ];
                }
            }
        }
    }

    public function dispatch(Request $request, callable $factory): Response
    {
        $httpMethod = strtoupper($request->getMethod());
        $path       = rtrim($request->getPathInfo() ?: '/', '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if (!in_array($httpMethod, $route['methods'], true)) {
                continue;
            }
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $args = [];
            foreach ($route['vars'] as $i => $paramName) {
                $val = $matches[$paramName] ?? ($matches[$i + 1] ?? null);
                if (is_string($val) && ctype_digit($val)) {
                    $val = (int) $val;
                }
                $args[] = $val;
            }

            $controller = $factory($route['class']);
            $result     = $controller->{$route['method']}(...$args);
            return $result instanceof Response ? $result : new JsonResponse($result, 200);
        }
        return new JsonResponse(['error' => ['code' => 'NOT_FOUND']], 404);
    }

    private function compile(string $path): array
    {
        $paramNames = [];
        $normalized = ($path === '/') ? '/' : rtrim($path, '/');
        $pattern    = '#\{(?P<name>[A-Za-z_]\w*)(?::(?P<constraint>[^}]+))?\}#';

        $regexBody = preg_replace_callback(
            $pattern,
            static function (array $m) use (&$paramNames): string {
                $defaultSegment = '[^/]+';
                $name           = $m['name'];
                $constraint     = $m['constraint'] ?? $defaultSegment;
                if ($constraint === '' || str_contains($constraint, '}')) {
                    $constraint = $defaultSegment;
                }
                $paramNames[] = $name;
                return '(?P<' . $name . '>' . $constraint . ')';
            },
            $normalized
        ) ?? '/';

        if ($regexBody !== '/') {
            $regexBody = rtrim($regexBody, '/');
        }
        return ['#^' . $regexBody . '$#', $paramNames];
    }
}

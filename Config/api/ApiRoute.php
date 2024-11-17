<?php

namespace Atpro\mvc\Config\api;

use JsonException;

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 */
class ApiRoute
{
    private string $path;
    private string $action;
    private array $parameters = [];
    private array $matches = [];

    public function __construct(string $path, string $action)
    {
        $this->path = $this->normalizePath($path);
        $this->action = $action;
        $this->extractParameters();
    }

    public function matches(string $url): bool
    {
        $pattern = $this->buildPattern();
        if (preg_match($pattern, $url, $matches)) {
            $this->matches = $matches;
            return true;
        }
        return false;
    }

    /**
     * @throws JsonException
     */
    public function execute(): void
    {
        [$controller, $method] = $this->parseAction();
        $params = $this->extractMatchedParameters();

        if (!class_exists($controller)) {
            throw new \RuntimeException("Controller $controller not found");
        }

        $instance = new $controller();
        if (!method_exists($instance, $method)) {
            throw new \RuntimeException("Method $method not found in $controller");
        }

        $response = $instance->$method(...$params);
        if (is_array($response)) {
            header('Content-Type: application/json');
            echo json_encode($response, JSON_THROW_ON_ERROR);
        }
    }

    private function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    private function extractParameters(): void
    {
        preg_match_all('/:(\w+)/', $this->path, $matches);
        $this->parameters = $matches[1];
    }

    private function buildPattern(): string
    {
        $pattern = preg_replace('/:(\w+)/', '([^/]+)', $this->path);
        return "#^$pattern$#";
    }

    private function parseAction(): array
    {
        return explode('@', $this->action);
    }

    private function extractMatchedParameters(): array
    {
        array_shift($this->matches);
        return array_values($this->matches);
    }
}

<?php

namespace Atpro\mvc\core;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController
{
    protected const TEMPLATE_EXT = ".twig";
    protected Environment $twig;
    protected Request $request;
    
    public function __construct()
    {
        $this->initializeTwig();
        $this->request = Request::createFromGlobals();
    }

    /**
     * Définit les rôles d'accès aux fonctions du controller
     * @return array Format: ['methodName' => ['ROLE_1', 'ROLE_2']]
     */
    abstract public function getAccess(): array;

    /**
     * Rend une vue avec les données fournies
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    protected function render(string $template, array $data = []): Response
    {
        $content = $this->twig->render($template . self::TEMPLATE_EXT, $data);
        return new Response($content);
    }

    /**
     * Retourne une réponse JSON
     */
    protected function json($data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Initialise l'environnement Twig
     */
    private function initializeTwig(): void
    {
        $loader = new FilesystemLoader('../' . VIEWS_FILES);
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => $_ENV['APP_ENV'] === 'dev',
            'auto_reload' => true
        ]);
    }
}

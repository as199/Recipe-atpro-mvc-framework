<?php

namespace Atpro\mvc\core;

use GlobalHelpers\Extensions\ExtensionTwig;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    const EXT = ".twig";

    /**
     * @return array
     * permet de definir les roles d'accés au fonction du controller
     * example: ['index'=>['ROLE_ADMIN','ROLE_SYS'],'login'=>[]]
     */
    abstract public function getAccess(): array;

    /**
     * @param string $fichier {{ la page }}
     * @param array $data {{ les données }}
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $fichier, array $data = []): void
    {
        $loader = new FilesystemLoader('../'.VIEWS_FILES);
        $twig = new Environment($loader, [
            'cache' => false,
        ]);
        /**
         * Pour inclures des liens css , js et des images se situant dans le dossier public
         */

        $twig->addExtension(new ExtensionTwig());
        echo $twig->render($fichier . self::EXT, $data);
    }
}

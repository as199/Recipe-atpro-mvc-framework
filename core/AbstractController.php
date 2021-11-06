<?php

namespace Atpro\mvc\core;

use GlobalHelpers\Extensions\ExtensionTwig;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * @author Assane Dione <atpro0290@gmail.com>
 */
abstract class AbstractController
{
    const EXT = ".twig";
    /**
     * @author Assane Dione <atpro0290@gmail.com>
     * @return array
     * permet de definir les roles d'accés au fonction du controller
     * example: ['index'=>['ROLE_ADMIN','ROLE_SYS'],'login'=>[]]
     */
    abstract public function getAccess(): array;

    /**
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $fichier {{ la page }}
     * @param array $data {{ les données }}
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $fichier, array $data = [])
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
    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }
}

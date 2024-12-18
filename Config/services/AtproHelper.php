<?php



//#region DEFINITION DES CONSTANTES DE L'APPLICATION
use Atpro\mvc\Config\services\Jwt;

define("VIEWS_FILES", $_ENV['VIEWS_FILES']);
define('ROOT', dirname(__DIR__));
define("PROTOCAL", $_ENV['ENV'] === 'development' ? 'http' : 'https');
define('HOST', PROTOCAL . '://' . $_SERVER['HTTP_HOST'] . '/');
const ASSETS = HOST;
define("REQUEST_TYPE", isset($_SERVER['REQUEST_SCHEME']));
$globalUrl = REQUEST_TYPE === true ?$_GET['url']:$_SERVER['REQUEST_URI'];
define("GLOBAL_URL", $globalUrl);
const FLASH = "flash";
const WEB = '/routes/web.php';
const API = '/routes/api.php';
//#endregion

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool
 * Permet de dire si la request provient d'un api
 */
function isAPI(): bool
{
    $urls = explode("/", filter_var(trim(GLOBAL_URL, "/"), FILTER_SANITIZE_URL));
    return $urls[0] === "api";
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool
 * Verifie si l'utilisateur est authentifié
 */
function isAuth(): bool
{
    return isset($_SESSION['auth']);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $key
 * @param string $code {{ 'success' or 'danger' }}
 * @param string $message
 */
function addFlash(string $key, string $code, string $message)
{
    $_SESSION[FLASH][] = [
        $key =>[
            'code'=>$code,
            'message'=>$message
            ]
    ];
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet d'ajouter une pile de message d'erreur de validation
 * @param array $data
 */
function addFlashs(array $data)
{
    destroyFlash();
    foreach ($data as $key => $value) {
        addFlash($key, 'danger', $value[0]);
    }
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de supprimer les messages flash
 */
function destroyFlash()
{
    unset($_SESSION[FLASH]);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param array $data
 * Permet d'ajouter des infos à la session
 */
function setSession(array $data)
{
    foreach ($data as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $role
 * @return string
 */
function getRole(string $role)
{
    $data = explode('_', $role);
    return ucfirst($data[1]);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $data
 * @throws JsonException
 */
function Json_response($data)
{
    header('Content-Type: application/json');
    header('Access-Control-Origin: *');
    echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $key
 * @return mixed|null
 * Permet de recuperer une information dans la session
 */
function getSession(string $key)
{
    return $_SESSION[$key] ?? null;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $key
 * @return bool retourne true si la session existe
 */
function sessionExist(string $key): bool
{
    return isset($_SESSION[$key]);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $key
 * @return void
 * Permet de supprimer une variable dans la session
 */
function removeSession(string $key): void
{
    unset($_SESSION[$key]);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $data
 */
function dd($data)
{
   echo '<pre>';
   print_r($data);
   echo '<pre>';
   die();
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $link The file directory
 * @return string
 * Permet d'inclures des fichiers css et js se trouvant dans le dossier public
 */
function asset(string $link): string
{
    return ASSETS . $link;
}
/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $password The user's password
 * @return string
 */
function encode_password(string $password):string {
    return  password_hash($password, PASSWORD_ARGON2I);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return string la methode de la request
 *
 */
function method(): string
{
    return strtolower($_SERVER['REQUEST_METHOD']);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool si la method est de type get
 */
function isGet(): bool
{
    return method() === 'get';
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool si la method est de type delete
 */
function isDelete(): bool
{
    return method() === 'delete';
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool si la method est de type put
 */
function isPut(): bool
{
    return method() === 'put';
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return bool Si la method est de type post
 */
function isPost(): bool
{
    return method() === 'post';
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return array
 * Permet de recuperer le contenu de la request
 */
function getBody()
{
    $body = [];
    if (isGet()) {
        foreach ($_GET as $key => $value) {
            $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }

    if (isPost()) {
        foreach ($_POST as $key => $value) {
            $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }
    return $body;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @return array|string|string[]|null
 * Permet de retourner le HTTP_USER_AGENT
 */
function getAgent_no_version()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $regex = '/\/[a-zA-Z0-9.]+/';
    return preg_replace($regex, '', $agent);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $name {{ le name }}
 * @param string $value {{ la valeur }}
 * @param int $expires {{ le temps expiration }}
 * @return bool
 * Permet d'ajouter un cookie
 */
function setCookies(string $name, string $value, int $expires = 604800)
{
    if (setcookie($name, $value, time() + $expires)) {
        return true;
    }
    return false;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $name
 * Permet de supprimer un cookie
 */
function removeCookie($name)
{
    setcookie($name, '', time() - 1);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $name
 * @return mixed permet de recuperer un cookie via le name
 */
function getCookie($name)
{
    return $_COOKIE[$name];
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $name
 * @return bool si le cookie existe
 */
function cookieExist(string $name)
{
    return isset($_COOKIE[$name]);
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param string $location
 * Permet de rediriger vers une url donnée
 */
function Redirect(string $location)
{
    if (!headers_sent()) {
        header("Location: " . HOST . $location);
    }

    echo '<script type="text/javascript">';
    echo 'window.location.href="' . HOST . $location . '";';
    echo '</script>';
    echo '<noscript>';
    echo '<meta http-equiv="refresh" content="0";url=' . $location . '" />';
    echo '</noscript>';
    exit();
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $dirty
 * @return string
 */
function sanitize($dirty)
{
    return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * @param $name
 * @return string|null
 */
function input($name)
{
    if (isset($_POST[$name])) {
        return sanitize($_POST[$name]);
    }
    if (isset($_GET[$name])) {
        return sanitize($_GET[$name]);
    }
    return null;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de verifier si on a le droit d'acceder à une method
 * @param $access
 * @param $action
 * @return bool
 */
function VerifierAccess($access, $action)
{
    $role = $_SESSION['role'] ?? true;
    if (isset($access[$action]) && !empty($access[$action])) {
        if (in_array($role, $access[$action], true)) {
            return true;
        }
        return false;
    }
    return true;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de verifier si on a le droit d'acceder à une method via l' api
 * @param $access
 * @param $action
 * @return bool
 * @throws JsonException
 */
function VerifierAccessApi($access, $action)
{
    $configs = (!empty($access[$action])) ? $access[$action] : null;
    $role = verifToken() ?? null;
    if ($configs !== null) {
        if (in_array($role, $configs, true)) {
            return true;
        }
        return false;
    }
    return true;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de gerer le header pour des appelles api
 * @param string $method
 */
function headers(string $method)
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: $method");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de recuperer le token
 * @return string
 */
function getToken()
{
    $token = null;
    if (isset($_SERVER['Authorization'])) {
        $token = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $token = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $token = trim($requestHeaders['Authorization']);
        }
    }
    return $token;
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de retourner le header du payload
 * @return string[]
 */
function headerPayload()
{
    return [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de surcharger le body du payload
 * @param int $id The user id
 * @param array $roles The user roles
 * @param string $email the user email
 * @param array $options The other options
 * @return array
 */
function setPayload(int $id, array $roles, string $email=NULL, array $options = [])
{
    return [
        'user_id' => $id,
        'roles' => $roles,
        'email' => $email,
        'infos' => $options
    ];
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de retourner le role de l'utilisateur via le token
 * @return mixed|null
 * @throws JsonException
 */
function verifToken(): mixed
{
    $token = getToken();
    if (!isset($token) || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
        return null;
    }
    $token = str_replace('Bearer ', '', $token);
    $jwt = new Jwt();
    if (!$jwt->isValid($token)) {
        http_response_code(400);
        return null;
    }
    if (!$jwt->check($token)) {
        http_response_code(403);
        return null;
    }
    if ($jwt->isExpired($token)) {
        http_response_code(403);
        return null;
    }
    $tokens = $jwt->getPayload($token);
    return $tokens['roles'][0];
}

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 * Permet de decoder le token
 * @throws JsonException
 */
function decodeToken()
{
    $token = getToken();
    if (!isset($token) || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
        http_response_code(400);
        echo json_encode(['message' => 'Token introuvable'], JSON_THROW_ON_ERROR);
        exit;
    }
    $token = str_replace('Bearer ', '', $token);
    $jwt = new Jwt();
    if (!$jwt->isValid($token)) {
        http_response_code(400);
        echo json_encode(['message' => 'Token invalid'], JSON_THROW_ON_ERROR);
        exit;
    }
    if (!$jwt->check($token)) {
        http_response_code(403);
        echo json_encode(['message' => 'Le token est invalid'], JSON_THROW_ON_ERROR);
        exit;
    }
    if ($jwt->isExpired($token)) {
        http_response_code(403);
        echo json_encode(['message' => 'Le token a expire'], JSON_THROW_ON_ERROR);
        exit;
    }
    echo json_encode($jwt->getPayload($token), JSON_THROW_ON_ERROR);
}

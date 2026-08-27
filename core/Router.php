<?php
// core/Router.php
class Router {
    public function route() {

        $url = $_GET['url'] ?? 'home/index'; // Default route

        $parts = explode('/', trim($url, '/'));
        $controllerName = ucfirst($parts[0]) . 'Controller'; // HomeController
        $method = $parts[1] ?? 'index';
//echo "Controller: $controllerName, Method: $method"; exit;
        if ($parts[0] === 'dashboard') {
            header('Location: /user');
            exit;
        }

        $controllerPath = '../app/controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new $controllerName();

            $params = array_slice($parts, 2); // get URL parts after controller and method

            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                echo "Method $method not found.";
            }
        } else {
            echo "Controller $controllerName not found.";
        }

    }
}

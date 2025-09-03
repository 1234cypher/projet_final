<?php
require_once __DIR__ . '/config.php';

class Router {
    private $routes = [];

    public function add($path, $controller, $method, $httpMethod = 'GET') {
        $this->routes[strtoupper($httpMethod)][$path] = [
            'controller' => CONTROLLERS_PATH . $controller . '.php',
            'method' => $method
        ];
    }

    public function route($url, $httpMethod, $queryParams = []) {
        $url = strtok($url, '?'); // Supprimer les paramètres de requête
        $url = rtrim($url, '/'); // Supprimer le slash final
        if (empty($url)) $url = '/';

        $httpMethod = strtoupper($httpMethod);


        // Ajout des routes par défaut si non définies via add()
        if (empty($this->routes)) {
            $this->add('/', 'HomeController', 'index');
            $this->add('/service/{id}', 'ServiceController', 'show');
            $this->add('/admin/message/{id}', 'AdminController', 'messageDetail');
        }

        // Debug logging
        error_log("Routing request: $httpMethod $url");
        if (!empty($this->routes[$httpMethod])) {
            error_log("Available routes for $httpMethod: " . implode(', ', array_keys($this->routes[$httpMethod])));
        }

        // Correspondance des routes exactes - PRIORITÉ AUX ROUTES EXPLICITEMENT AJOUTÉES
        if (isset($this->routes[$httpMethod][$url])) {
            $route = $this->routes[$httpMethod][$url];
            error_log("Exact route match found: $url -> " . $route['controller'] . '::' . $route['method']);
            return $this->dispatch($route['controller'], $route['method'], [], $queryParams);
        }

        // Correspondance des routes dynamiques
        if (isset($this->routes[$httpMethod])) {
            $params = [];
            foreach ($this->routes[$httpMethod] as $path => $route) {
                // Skip exact matches that we already checked
                if ($path === $url) continue;
                
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([0-9a-zA-Z_-]+)', $path);
                $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
                if (preg_match($pattern, $url, $matches)) {
                    array_shift($matches); // Supprimer la correspondance complète
                    $params = $matches;
                    error_log("Dynamic route match found: $url -> " . $route['controller'] . '::' . $route['method'] . " with params: " . json_encode($params));
                    return $this->dispatch($route['controller'], $route['method'], $params, $queryParams);
                }
            }
        }

        error_log("No route found for: $httpMethod $url");
        $this->notFound();
    }

    private function dispatch($controllerPath, $method, $params = [], $queryParams = []) {
        if (!file_exists($controllerPath)) {
            error_log("Fichier contrôleur introuvable : $controllerPath");
            $this->notFound();
        }

        require_once $controllerPath;
        $controllerName = basename($controllerPath, '.php');
        if (!class_exists($controllerName)) {
            error_log("Classe contrôleur introuvable : $controllerName");
            $this->notFound();
        }

        $controller = new $controllerName();
        if (!method_exists($controller, $method)) {
            error_log("Méthode introuvable : $method dans $controllerName");
            $this->notFound();
        }

        // Passer les paramètres et paramètres de requête à la méthode
        return call_user_func_array([$controller, $method], array_merge($params, [$queryParams]));
    }

    private function notFound() {
        http_response_code(404);
        if (file_exists(VIEWS_PATH . '404.php')) {
            include VIEWS_PATH . '404.php';
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Page non trouvée';
        }
        exit;
    }

    private function errorResponse($statusCode, $message) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>

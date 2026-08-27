<?php
// core/Controller.php

class Controller {
    // Optional: common controller functionality
    public function loadModel($modelName) {
        $path = "../app/models/" . $modelName . ".php";
        if (file_exists($path)) {
            require_once $path;
            return new $modelName();
        }
        return null;
    }

    public function renderView($viewPath, $data = []) {
        extract($data);
        require_once "../app/views/" . $viewPath . ".php";
    }
}

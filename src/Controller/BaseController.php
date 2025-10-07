<?php

namespace App\Controller;

use App\Routing\Router;

class BaseController
{

    public function __construct(protected Router $router) {}

    protected function render(string $template, string $titlePage, array $data = []): string
    {
        foreach ($data as $key => $value) {
            //carpools = [ //données des covoiturages ]] égal à :
            $$key = $value;
        }

        unset($data);

        $current = $this->router->getCurrentPage();


        ob_start(); //tout ce qui va après jusqu'au ob_clean : sera mis en cache

        require_once MAIN_TEMPLATE_PATH;

        $content = ob_get_contents();
        ob_clean();

        return $content;
    }

    protected function renderPartial(string $template, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        unset($data);

        $current = $this->router->getCurrentPage();

        ob_start();
        require TEMPLATE_PATH . '/' . $template;
        echo ob_get_clean();
    }
}

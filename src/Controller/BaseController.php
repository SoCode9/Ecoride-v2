<?php

namespace App\Controller;

class BaseController
{
    protected function render(string $template, array $data=[]): string
    {
        foreach($data as $key => $value){
            //carpools = [ //données des covoiturages ]] égal à :
            $$key = $value;
        }

        unset($data);

        ob_start(); //tout ce qui va après jusqu'au ob_clean : sera mis en cache
        require_once TEMPLATE_PATH;

        $content = ob_get_contents();
        ob_clean();

        return $content;
    }
}

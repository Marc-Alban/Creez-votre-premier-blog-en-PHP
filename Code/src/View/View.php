<?php

declare(strict_types=1);

namespace App\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class View
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($loader);
    }

    public function render(string $path, string $view, ?array $data): void
    {
        echo $this->twig->render($path.'/'.$view.'.html.twig',['data' =>$data]);
    }
}

<?php

declare(strict_types=1);

namespace App\View;

use Twig\Environment;
use App\Service\Http\Session;
use Twig\Loader\FilesystemLoader;


final class View
{
    private Environment $twig;
    private Session $session;
    private FilesystemLoader $loader;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($this->loader);
    }

    public function render(string $path, string $view, ?array $data): void
    {
        $this->twig->addGlobal('session', $this->session->getSession());
        echo $this->twig->render($path.'/'.$view.'.html.twig', ['data' =>$data]);
    }
}

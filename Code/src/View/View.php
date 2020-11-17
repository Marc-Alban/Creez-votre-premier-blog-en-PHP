<?php
declare(strict_types=1);
namespace App\View;

use App\Service\Http\Session;
use Twig\Environment;
use Twig\Extension\DebugExtension;
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
        $this->twig = new Environment($this->loader, ['debug'=>true]);
        $this->twig->addExtension(new DebugExtension());
    }
    /**
     * Displays the correct view with the parameters passed
     *
     * @param string $path
     * @param string $view
     * @param array|null $data
     * @return void
     */
    public function render(string $path, string $view, ?array $data): void
    {
        $this->twig->addGlobal('session', $this->session->getSession());
        echo $this->twig->render($path.'/'.$view.'.html.twig', ['data' => $data]);
    }
    /**
     * Returns the view of the display of an email
     *
     * @param array $data
     * @return string
     */
    public function renderMail(array $data): string
    {
        $this->twig->addGlobal('session', $this->session->getSession());
        return $this->twig->render('frontoffice/mail.html.twig', ['data' => $data]);
    }
}

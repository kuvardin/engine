<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine;

use App\Sessions\Session;
use App\Sites\Input\SiteInput;
use Kuvardin\ChartJS\Chart;
use RuntimeException;

/**
 * Class Page
 *
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Page
{
    public string $title;
    public ?string $subtitle = null;
    public ?string $description;
    protected int $http_status = 200;
    protected ?string $redirect_uri = null;

    /** @var string[] */
    public array $keywords = [];

    /**
     * @var bool Метка "сообщить поисковикам не индексировать данную страницу"
     */
    public bool $no_indexing;

    /**
     * @var string Контент страницы
     */
    public string $content = '';

    /**
     * @var string[]
     */
    public array $errors = [];

    /**
     * @var string[] Дополнительные CSS-файлы
     */
    public array $css_files = [];

    /**
     * @var string[] Дополнительные JS-файлы в тэге head
     */
    public array $js_files_in_head = [];

    /**
     * @var string[] Дополнительные JS-файлы внизу страницы
     */
    public array $js_files_in_bottom = [];

    /**
     * @var bool Метка "не использовать шаблон main.php"
     */
    public bool $not_use_main_template = false;

    /**
     * @var Chart[] ChartJS charts list
     */
    public array $chartjs_charts = [];

    /**
     * @var Breadcrumb[]
     */
    public array $breadcrumbs = [];

    /**
     * @var Alert[]
     */
    public array $alerts = [];

    /**
     * @var Link[]
     */
    public array $header_links = [];

    public ?int $counter = null;

    public function __construct(string $title, string $description = null, bool $no_indexing = false)
    {
        $this->title = $title;
        $this->description = $description;
        $this->no_indexing = $no_indexing;
    }

    public function render(Session $session, string $template_path, SiteInput $input, array $data = []): string
    {
        return TemplatesEngine::render($template_path, array_merge($data, [
            'page' => $this,
            'session' => $session,
            'lang' => $session->getLanguage(),
            'input' => $input,
        ]));
    }

    public function getHttpStatus(): int
    {
        return $this->http_status;
    }

    public function setHttpStatus(int $http_status): void
    {
        if ($http_status < 100 || $http_status > 599) {
            throw new RuntimeException("Incorrect HTTP-status: $http_status");
        }

        if ($this->redirect_uri !== null && $http_status !== 301 && $http_status !== 302) {
            throw new RuntimeException("Incorrect redirect HTTP-status: $http_status (must be 301 or 302)");
        }

        $this->http_status = $http_status;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirect_uri;
    }

    public function setRedirectUri(?string $redirect_uri, bool $temporarily = true): void
    {
        $this->redirect_uri = $redirect_uri;
        if ($redirect_uri !== null) {
            $this->http_status = $temporarily ? 302 : 301;
        }
    }
}
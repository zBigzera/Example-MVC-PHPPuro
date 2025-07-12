<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* site/base.twig.html */
class __TwigTemplate_dd1f74bfbf64012e6147db76c11463f3 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'content' => [$this, 'block_content'],
            'scripts' => [$this, 'block_scripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!doctype html>
<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <!-- Bloco pra CSS personalizado -->
     ";
        // line 9
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 10
        yield "  </head>
  <body class=\"bg-dark text-light\">
    <div class=\"container\">
      ";
        // line 13
        yield from $this->load("site/components/header.twig.html", 13)->unwrap()->yield($context);
        // line 14
        yield "      
      ";
        // line 15
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 16
        yield "

      <!-- Se não quiser usar footer em uma página específica, 
       mesmo usando include, 
       é so envolver com um bloco, 
       e na pagina específica trocar o 
       conteúdo do bloco para vazio -->
      ";
        // line 23
        yield from $this->load("site/components/footer.twig.html", 23)->unwrap()->yield($context);
        // line 24
        yield "    </div>

    <!-- Bloco pra JS personalizado -->
    ";
        // line 27
        yield from $this->unwrap()->yieldBlock('scripts', $context, $blocks);
        // line 29
        yield "  </body>
</html>
";
        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "Página")) : ("Página")), "html", null, true);
        yield from [];
    }

    // line 9
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 15
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 27
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_scripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 28
        yield "    ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "site/base.twig.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  136 => 28,  129 => 27,  119 => 15,  109 => 9,  98 => 6,  91 => 29,  89 => 27,  84 => 24,  82 => 23,  73 => 16,  71 => 15,  68 => 14,  66 => 13,  61 => 10,  59 => 9,  53 => 6,  46 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "site/base.twig.html", "/var/www/html/resources/view/site/base.twig.html");
    }
}

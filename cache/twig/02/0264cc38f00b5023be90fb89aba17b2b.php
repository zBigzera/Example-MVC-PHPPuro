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

/* site/pages/testimonies/index.twig.html */
class __TwigTemplate_67648d758e59af33a03e2f86a4d0201d extends Template
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

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "site/base.twig.html";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("site/base.twig.html", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 4
        yield "<h1>Depoimentos</h1>
<hr>
<section id=\"testimonies\">
  ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["itens"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 8
            yield "    ";
            yield from $this->load("site/pages/testimonies/item.twig.html", 8)->unwrap()->yield(CoreExtension::merge($context, $context["item"]));
            // line 9
            yield "  ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        yield "  
  ";
        // line 11
        if ((($tmp =  !Twig\Extension\CoreExtension::testEmpty(($context["pagination"] ?? null))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 12
            yield "      ";
            yield from $this->load("site/components/pagination.twig.html", 12)->unwrap()->yield(CoreExtension::merge($context, ($context["pagination"] ?? null)));
            // line 13
            yield "  ";
        }
        // line 14
        yield "</section>
<hr>

<h3>Envie seu depoimento</h3>
<form method=\"post\" action=\"?page=1\">
  <div class=\"form-group\">
    <label>Nome</label>
    <input type=\"toext\" class=\"form-control\" name=\"nome\" required>
  </div>
  <div class=\"form-group\">
    <label class=\"form-label\">Mensagem</label>
    <textarea class=\"form-control\" rows=\"5\" name=\"mensagem\" required></textarea>
  </div>
  <div class=\"form-group mt-3\">
    <button type=\"submit\" class=\"btn btn-success\">Enviar</button>
  </div>
</form>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "site/pages/testimonies/index.twig.html";
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
        return array (  108 => 14,  105 => 13,  102 => 12,  100 => 11,  97 => 10,  83 => 9,  80 => 8,  63 => 7,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "site/pages/testimonies/index.twig.html", "/var/www/html/resources/view/site/pages/testimonies/index.twig.html");
    }
}

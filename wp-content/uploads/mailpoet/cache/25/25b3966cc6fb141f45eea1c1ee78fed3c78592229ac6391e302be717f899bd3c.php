<?php

use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* subscription/confirm_unsubscribe.html */
class __TwigTemplate_0148bbe67e6eed23d1bc13f217bde1f086fa914565fc493d0f0e6bd2094d5092 extends \MailPoetVendor\Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $this->displayBlock('content', $context, $blocks);
    }

    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        echo "<p class=\"mailpoet_confirm_unsubscribe\">
  ";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Simply click on this link to stop receiving emails from us.");
        echo "
  <br>
  <a href=\"";
        // line 5
        echo \MailPoetVendor\twig_escape_filter($this->env, ($context["unsubscribeUrl"] ?? null), "html", null, true);
        echo "\" rel=\"nofollow\">";
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Yes, unsubscribe me", "Text in unsubscribe link");
        echo "</a>
</p>
";
    }

    public function getTemplateName()
    {
        return "subscription/confirm_unsubscribe.html";
    }

    public function getDebugInfo()
    {
        return array (  53 => 5,  48 => 3,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "subscription/confirm_unsubscribe.html", "/var/www/u1263204/data/www/test.mtamoscow.ru/ihes/wp-content/plugins/mailpoet/views/subscription/confirm_unsubscribe.html");
    }
}

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

/* newsletter/templates/blocks/image/block.hbs */
class __TwigTemplate_4983629891333b0205bfd2680ba5ed355a773ccdc0492a782ae8821ca87a759e extends \MailPoetVendor\Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div class=\"mailpoet_tools\"></div>
<div class=\"mailpoet_content\" style=\"{{#ifCond model.styles.block.textAlign '==' 'left'}}margin: 0 auto 0 0; {{/ifCond}}{{#ifCond model.styles.block.textAlign '==' 'center'}}margin: auto; {{/ifCond}}{{#ifCond model.styles.block.textAlign '==' 'right'}}margin: 0 0 0 auto; {{/ifCond}}width: {{model.width}}\">
\t<div class=\"mailpoet_image\">
\t\t<a href=\"{{ model.link }}\" onClick=\"return false;\">
\t\t\t<img src=\"{{#ifCond model.src '!=' ''}}{{ model.src }}{{ else }}{{ imageMissingSrc }}{{/ifCond}}\" alt=\"{{ model.alt }}\" onerror=\"if(this.src != '{{ imageMissingSrc }}') {this.src = '{{ imageMissingSrc }}';}\" width=\"{{model.width}}\" />
\t\t</a>
\t\t<div class=\"mailpoet_image_resize_handle_container{{#ifCond model.styles.block.textAlign '==' 'right'}} mailpoet_image_resize_handle_container_left{{/ifCond}}\">
\t\t    <div class=\"mailpoet_image_resize_handle\">
\t\t        <span class=\"mailpoet_image_resize_handle_icon\">";
        // line 9
        echo \MailPoetVendor\twig_source($this->env, "newsletter/templates/svg/block-tools/resize.svg");
        echo "</span>
\t\t    </div>
\t\t</div>
\t</div>
</div>
<div class=\"mailpoet_block_highlight\"></div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/image/block.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 9,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/image/block.hbs", "/var/www/u1263204/data/www/test.mtamoscow.ru/ihes/wp-content/plugins/mailpoet/views/newsletter/templates/blocks/image/block.hbs");
    }
}

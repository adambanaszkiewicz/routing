<?php

namespace Requtize\Routing;

class RouteDefinitionCompiler implements RouteDefinitionCompilerInterface
{
    protected $predefinedRules = [
        'alnum'  => '[0-9a-zA-Z]+',
        'alnumd' => '[0-9a-zA-Z\-]+',
        'number' => '[0-9]+',
        'digit'  => '[0-9]',
        'alpha'  => '[a-zA-Z]+',
        'word'   => '\w+'
    ];

    public function compile(Route $route)
    {
        $tokens = [];
        $source = str_replace(['/', '[', ']', '(', ')'], ['\/', '\[', '\]', '\(', '\)'], $route->getSourceRoute());

        preg_match_all('#\{\w+\}#', $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $name = substr($match[0][0], 1, -1);

            $regexpSource = $this->predefinedRules['alnumd'];

            $rule = $route->getRuleByName($name);

            if(isset($this->predefinedRules[$rule]))
            {
                $regexpSource = $this->predefinedRules[$rule];
            }
            elseif($rule)
            {
                $regexpSource = $rule;
            }

            $regexp = "({$regexpSource})";

            $tokens[] = [
                'name'     => $name,
                'pattern'  => $regexpSource,
                'placeholder' => $match[0][0]
            ];

            $source = str_replace($match[0][0], $regexp, $source);
        }

        $route->setTokens($tokens);
        $route->setRoute("/^{$source}$/");

        return $route;
    }

    public function addPredefinedRule($name, $regexp)
    {
        $this->predefinedRules[$name] = $regexp;

        return $this;
    }
}

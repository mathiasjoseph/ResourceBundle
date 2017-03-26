<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Controller;

use Miky\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;


class ParametersParser implements ParametersParserInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expression;

    /**
     * @param ExpressionLanguage $expression
     */
    public function __construct(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function parseRequestValues(array $parameters, Request $request)
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parseRequestValues($value, $request);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameterName = substr($value, 1);
                $parameters[$key] = $request->get($parameterName);
            }

            if (is_string($value) && 0 === strpos($value, 'expr:')) {
                $parameters[$key] = $this->expression->evaluate(substr($value, 5));
            }
        }

        return $parameters;
    }
}

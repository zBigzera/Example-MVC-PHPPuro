<?php

namespace App\Utils;

/**
 * Classe responsável por renderizar views com suporte a templates simples
 * 
 * Fornece funcionalidades básicas de template engine, incluindo:
 * - Substituição de variáveis
 * - Estruturas de controle (if/elseif/else, foreach)
 * - Avaliação de expressões simples
 */
class View
{
    /**
     * @var array Variáveis globais disponíveis para todas as views
     */
    private static $vars = [];
    
    /**
     * Inicializa a classe View com variáveis globais
     * 
     * @param array $vars Variáveis que estarão disponíveis em todas as views
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    /**
     * Obtém o conteúdo de um arquivo de view
     * 
     * @param string $view Nome do arquivo de view (sem extensão)
     * @return string Conteúdo do arquivo ou string vazia se não existir
     */
    private static function getContentView($view)
    {
        $file = __DIR__ . "/../../resources/view/" . $view . ".html";
        return file_exists($file) ? file_get_contents($file) : "";
    }

    /**
     * Avalia uma condição complexa (com operadores lógicos && e ||)
     * 
     * @param string $expr Expressão condicional a ser avaliada
     * @param array $vars Variáveis disponíveis para avaliação
     * @return bool Resultado da avaliação da condição
     */
    private static function evaluateCondition($expr, $vars)
    {
        // Remove espaços em excesso
        $expr = trim($expr);

        // Se não contém operadores lógicos, avalia como comparação simples
        if (!preg_match('/(&&|\|\|)/', $expr)) {
            return self::compare($expr, $vars);
        }

        // Divide a expressão em partes lógicas (operandos e operadores)
        $parts = preg_split('/\s*(&&|\|\|)\s*/', $expr, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Avalia o primeiro operando
        $result = self::compare($parts[0], $vars);

        // Processa os operadores e operandos subsequentes
        for ($i = 1; $i < count($parts); $i += 2) {
            $operator = $parts[$i];
            $operand = self::compare($parts[$i + 1], $vars);

            if ($operator === '&&') {
                $result = $result && $operand;
                // Short-circuit evaluation: se false, interrompe avaliação
                if (!$result)
                    break;
            } else { // operador ||
                $result = $result || $operand;
                // Short-circuit evaluation: se true, interrompe avaliação
                if ($result)
                    break;
            }
        }

        return $result;
    }

    /**
     * Avalia uma expressão simples (variável, string, número ou booleano)
     * 
     * @param string $expr Expressão a ser avaliada
     * @param array $vars Variáveis disponíveis para avaliação
     * @return mixed Valor resultante da avaliação
     */
    private static function evaluateExpression($expr, $vars)
    {
        // Remove espaços
        $expr = trim($expr);

        // Se for string entre aspas
        if (preg_match('/^[\'"](.*?)[\'"]$/', $expr, $matches)) {
            return $matches[1];
        }

        // Se for número
        if (is_numeric($expr)) {
            return $expr + 0; // Converte para int ou float
        }

        // Se for variável existente no array
        if (array_key_exists($expr, $vars)) {
            return $vars[$expr];
        }

        // Se for booleano literal
        if ($expr === 'true')
            return true;
        if ($expr === 'false')
            return false;

        // Tenta avaliar como expressão matemática simples
        try {
            extract($vars);
            return eval("return ($expr);");
        } catch (\ParseError $e) {
            return false;
        }
    }

    /**
     * Compara dois valores usando operadores de comparação
     * 
     * @param string $expr Expressão de comparação (ex: "a > b", "x == y")
     * @param array $vars Variáveis disponíveis para avaliação
     * @return bool Resultado da comparação
     */
    private static function compare($expr, $vars)
    {
        // Importa variáveis para o escopo atual
        extract($vars);

        // Verifica se é uma comparação válida (com operador)
        if (preg_match('/^(.+?)\s*(==|!=|<=|>=|<|>)\s*(.+)$/', $expr, $matches)) {
            $left = trim($matches[1]);
            $op = trim($matches[2]);
            $right = trim($matches[3]);

            // Avalia os dois lados da comparação
            $leftVal = self::evaluateExpression($left, $vars);
            $rightVal = self::evaluateExpression($right, $vars);

            // Executa a operação de comparação apropriada
            switch ($op) {
                case '==':
                    return $leftVal == $rightVal;
                case '!=':
                    return $leftVal != $rightVal;
                case '<=':
                    return $leftVal <= $rightVal;
                case '>=':
                    return $leftVal >= $rightVal;
                case '<':
                    return $leftVal < $rightVal;
                case '>':
                    return $leftVal > $rightVal;
            }
        }

        // Se não for comparação, avalia como expressão booleana simples
        return (bool) self::evaluateExpression($expr, $vars);
    }

    /**
     * Renderiza uma view com as variáveis fornecidas
     * 
     * @param string $view Nome da view a ser renderizada
     * @param array $vars Variáveis específicas para esta renderização
     * @return string Conteúdo renderizado
     */
    public static function render($view, $vars = [])
    {
        // Obtém o conteúdo do arquivo de view
        $content = self::getContentView($view);
        
        // Combina variáveis globais com as específicas da view
        $vars = array_merge(self::$vars, $vars);

        // Processa estruturas de controle (if, foreach)
        $content = self::parseControlStructures($content, $vars);

        // Substitui variáveis simples ({{ variavel }})
        $content = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function ($matches) use ($vars) {
            return $vars[$matches[1]] ?? '';
        }, $content);

        return $content;
    }

    /**
     * Processa estruturas de controle (@if, @foreach) no conteúdo
     * 
     * @param string $content Conteúdo a ser processado
     * @param array $vars Variáveis disponíveis para avaliação
     * @return string Conteúdo com as estruturas de controle processadas
     */
    private static function parseControlStructures($content, $vars)
    {
        // Processa blocos @if/@elseif/@else/@endif
        $content = preg_replace_callback(
            '/@if\s*\((.*?)\)\s*([\s\S]*?)((?:\s*@elseif\s*\(.*?\)\s*[\s\S]*?)*)(?:\s*@else\s*([\s\S]*?))?\s*@endif/s',
            function ($matches) use ($vars) {
                // 1. Verifica a condição do @if principal
                if (self::evaluateCondition($matches[1], $vars)) {
                    return $matches[2];
                }

                // 2. Processa todos os @elseif
                if (!empty($matches[3])) {
                    // Extrai cada bloco elseif individualmente
                    preg_match_all(
                        '/@elseif\s*\((.*?)\)\s*([\s\S]*?)(?=\s*@elseif|\s*@else|\s*@endif)/s',
                        $matches[0], // Analisa o conteúdo ORIGINAL completo
                        $elseifs,
                        PREG_SET_ORDER
                    );

                    foreach ($elseifs as $elseif) {
                        if (self::evaluateCondition($elseif[1], $vars)) {
                            return $elseif[2]; // Retorna o conteúdo do primeiro elseif verdadeiro
                        }
                    }
                }

                // 3. Retorna @else (se existir) se nenhuma condição anterior for verdadeira
                return $matches[4] ?? '';
            },
            $content
        );

        // Processa blocos @foreach
        $content = preg_replace_callback(
            '/@foreach\s*\((.*?)\s+as\s+(.*?)\)\s*(.*?)@endforeach/s',
            function ($matches) use ($vars) {
                // Obtém o array a ser iterado
                $array = self::getVariableValue($matches[1], $vars);
                $itemName = trim($matches[2]);
                $loopContent = $matches[3];

                $result = '';
                if (is_array($array)) {
                    foreach ($array as $item) {
                        // Cria um novo escopo com a variável do item atual
                        $newVars = $vars;
                        $newVars[$itemName] = $item;
                        // Renderiza o conteúdo do loop com as novas variáveis
                        $result .= self::renderString($loopContent, $newVars);
                    }
                }
                return $result;
            },
            $content
        );

        return $content;
    }

    /**
     * Obtém o valor de uma variável ou avalia uma expressão simples
     * 
     * @param string $expression Nome da variável ou expressão a ser avaliada
     * @param array $vars Variáveis disponíveis
     * @return mixed Valor da variável ou resultado da expressão
     */
    private static function getVariableValue($expression, $vars)
    {
        $expression = trim($expression);
        return $vars[$expression] ?? eval("return $expression;");
    }

    /**
     * Renderiza uma string com substituição de variáveis
     * 
     * @param string $content Conteúdo a ser renderizado
     * @param array $vars Variáveis disponíveis para substituição
     * @return string Conteúdo renderizado
     */
    private static function renderString($content, $vars)
    {
        // Substitui variáveis no formato {{ variavel }}
        return preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function ($matches) use ($vars) {
            return $vars[$matches[1]] ?? '';
        }, $content);
    }
}
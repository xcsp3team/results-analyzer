<?php
/**
 * Evaluate grammar expressions for filtering benchmarks
 *   d > 0 and c > 0 or v > 0
 *   (d > 0 and c > 0) or (v >= 5 and d != 3)
 *
 * Supported grammar :
 *   expr       := term (OR term)*
 *   term       := factor (AND factor)*
 *   factor     := '(' expr ')' | predicate
 *   predicate  := VAR OP NUMBER
 *   VAR        := 'd' | 'c' | 'v'
 *   OP         := '>=' | '<=' | '!=' | '==' | '>' | '<'
 *
 * Usage :
 *   $eval = new ExprEvaluator(['d' => 5, 'c' => 0, 'v' => -1]);
 *   $result = $eval->evaluate('d > 0 and c > 0 or v > 0'); // bool
 */

namespace App\Misc;

class ExprEvaluatorException extends \RuntimeException
{
}

final class ExprEvaluator
{
    /** @var array<string,int|float> */
    private array $vars;

    /** @var array<int, array{type:string, value:string}> */
    private array $tokens = [];

    private int $pos = 0;

    private const ALLOWED_VARS = ['d', 'c', 'v'];

    public function __construct(array $vars)
    {
        foreach ($vars as $name => $value) {
            if (!in_array($name, self::ALLOWED_VARS, true))
                throw new ExprEvaluatorException("Unauthorized variables: $name");

            if (!is_int($value))
                throw new ExprEvaluatorException("Non numerical value:  $name");
        }
        $this->vars = $vars;
    }

    public function evaluate(string $expression): bool
    {
        $this->tokens = $this->tokenize($expression);
        $this->pos = 0;

        $result = $this->parseExpr();

        if ($this->pos !== count($this->tokens))
            throw new ExprEvaluatorException("Unexpected token, position {$this->pos}");


        return $result;
    }

    // ---- Tokenizer ---------------------------------------------------

    private function tokenize(string $expr): array
    {
        $pattern = <<<'REGEX'
        /\s*(
            \(|\)
            |and\b|or\b
            |>=|<=|!=|==|>|<
            |[dcv]\b
            |-?\d+(?:\.\d+)?)\s*/ix
        REGEX;

        if (!preg_match_all($pattern, $expr, $matches, PREG_OFFSET_CAPTURE)) {
            throw new ExprEvaluatorException("Can not parse expression: $expr");
        }

        // Vérifie qu'on a bien consommé toute la chaîne (pas de caractère
        // parasite non reconnu par le pattern entre deux tokens valides).
        $consumed = 0;
        foreach ($matches[0] as [$full, $offset]) {
            if ($offset !== $consumed) {
                $bad = substr($expr, $consumed, $offset - $consumed);
                throw new ExprEvaluatorException("unexpected characters : '$bad'");
            }
            $consumed = $offset + strlen($full);
        }
        if ($consumed !== strlen($expr)) {
            throw new ExprEvaluatorException(
                "Invalid characters : '" . substr($expr, $consumed) . "'"
            );
        }

        $tokens = [];
        foreach ($matches[1] as [$value, $offset]) {
            $tokens[] = ['type' => $this->classify($value), 'value' => $value];
        }
        return $tokens;
    }

    private function classify(string $value): string
    {
        $lower = strtolower($value);
        return match (true) {
            $value === '(' => 'LPAREN',
            $value === ')' => 'RPAREN',
            $lower === 'and' => 'AND',
            $lower === 'or' => 'OR',
            in_array($value, ['>=', '<=', '!=', '==', '>', '<'], true) => 'OP',
            in_array($lower, self::ALLOWED_VARS, true) => 'VAR',
            is_numeric($value) => 'NUMBER',
            default => throw new ExprEvaluatorException("Jeton inconnu : '$value'"),
        };
    }

    // ---- Parser récursif descendant + évaluation directe --------------

    private function peek(): ?array
    {
        return $this->tokens[$this->pos] ?? null;
    }

    private function consume(string $expectedType): array
    {
        $tok = $this->peek();
        if ($tok === null || $tok['type'] !== $expectedType) {
            $found = $tok['value'] ?? 'fin de chaîne';
            throw new ExprEvaluatorException("Expected $expectedType, found '$found'");
        }
        $this->pos++;
        return $tok;
    }

    private function parseExpr(): bool
    {
        $result = $this->parseTerm();
        while (($tok = $this->peek()) !== null && $tok['type'] === 'OR') {
            $this->consume('OR');
            $right = $this->parseTerm();
            $result = $result || $right; // pas de court-circuit : on valide toute la syntaxe
        }
        return $result;
    }

    private function parseTerm(): bool
    {
        $result = $this->parseFactor();
        while (($tok = $this->peek()) !== null && $tok['type'] === 'AND') {
            $this->consume('AND');
            $right = $this->parseFactor();
            $result = $result && $right;
        }
        return $result;
    }

    private function parseFactor(): bool
    {
        $tok = $this->peek();
        if ($tok === null) {
            throw new ExprEvaluatorException("Incomplete expression");
        }

        if ($tok['type'] === 'LPAREN') {
            $this->consume('LPAREN');
            $result = $this->parseExpr();
            $this->consume('RPAREN');
            return $result;
        }

        return $this->parsePredicate();
    }

    private function parsePredicate(): bool
    {
        $varTok = $this->consume('VAR');
        $opTok = $this->consume('OP');
        $numTok = $this->consume('NUMBER');

        $varName = strtolower($varTok['value']);
        if (!array_key_exists($varName, $this->vars)) {
            throw new ExprEvaluatorException("Variable '$varName' not furnished");
        }

        $left = $this->vars[$varName];
        $right = str_contains($numTok['value'], '.')
            ? (float)$numTok['value']
            : (int)$numTok['value'];

        return match ($opTok['value']) {
            '>' => $left > $right,
            '<' => $left < $right,
            '>=' => $left >= $right,
            '<=' => $left <= $right,
            '==' => $left == $right,
            '!=' => $left != $right,
            default => throw new ExprEvaluatorException("Opérateur inconnu : {$opTok['value']}"),
        };
    }
}

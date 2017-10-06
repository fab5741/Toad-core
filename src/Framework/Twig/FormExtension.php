<?php

namespace Framework\Twig;

use DateTime;

class FormExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ["html"],
                'needs_context' => true,
            ])
        ];
    }

    /**
     * @param $context
     * @param string $key
     * @param $value
     * @param null|string $label
     * @param array $options
     * @return string
     */
    public function field($context, string $key, $value, ? string $label = null, array $options = [])
    {
        $class = 'form-group';
        $type = $options['Type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $key,
            'id' => $key,
        ];
        if (!empty($options['containerClass'])) {
            $class .= " " . $options['containerClass'];
        }
        $value = $this->convertValue($value);
        if ($error) {
            $class .= " has-danger";
            $attributes['class'] .= ' form-control-danger';
        }
        if ($type == 'textarea') {
            $input = $this->textarea($value, $attributes, (int)$options['rows'] ?? null, (int)$options['cols'] ?? null);
        } elseif ($type === "file") {
            $input = $this->file($attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }

        return "<div class=\"{$class}\">
                <label class=\"form-control-label\" for=\"name\">{$label}</label>
                {$input}
                {$error}
            </div>";
    }

    /**
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
    }

    private function convertValue($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:S');
        } else {
            return (string)$value;
        }
    }


    /**
     * @param null|string $value
     * @param array $attributes
     * @param int $rows
     * @param int|null $cols
     * @return string
     */
    private function textarea(string $value, array $attributes, int $rows = null, int $cols = null): string
    {
        $rows = "rows=" . $rows ?? "rows={$rows}";
        $cols = "cols=" . $cols ?? "rows={$cols}";
        return "<textarea " . $this->getHtmlFromArray($attributes) . " " . $rows . " " . $cols . ">{$value}</textarea>";
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string)$key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
    }

    private function file($attributes)
    {
        return "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . "\">";
    }

    /**
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . "<option " . $this->getHtmlFromArray($params) . ">" . $options[$key] . "</option>";
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">" . $htmlOptions . "</select>";
    }

    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }
}

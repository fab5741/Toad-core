<?php

namespace Framework;

use DateTime;
use Framework\Validator\ValidationError;
use PDO;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
    ];

    /**
     * @var array
     */
    private $params;
    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * Validator constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string[] ...$keys
     * @return $this
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param string $rule
     * @param array $attributes
     * @internal param array $prams
     */
    private function addError(string $key, string $rule, array $attributes = [])
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * @param string[] ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * @return ValidationError[]
     * @internal param string[] ...$keys
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return Validator
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) and !is_null($max) and ($length < $min or $length > $max)) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) and $length < $min) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) and $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^([a-z0-9])+(-[a-z0-9]+)*$/';
        if (!is_null($value) and !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function dateTime(string $key, $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $dateTime = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 or $errors['warning_count'] > 0 or $dateTime == false) {
            $this->addError($key, 'dateTime', [$format]);
        }

        return $this;
    }

    public function exists(string $key, string $table, Pdo $pdo): self
    {
        $statement = $pdo->prepare("SELECT id FROM {$table} WHERE id= ?");
        $statement->execute([$this->getValue($key)]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @param string $key
     * @param string $table
     * @param PDO $pdo
     * @param int|null $exclude
     * @return Validator
     */
    public function unique(string $key, string $table, Pdo $pdo, int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM {$table} WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * Check if uploaded
     * @param string $key
     * @return Validator
     */
    public function uploaded(string $key): self
    {
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    /**
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self
    {
        /* @var UploadedFileInterface */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }

    public function email(string $key): self
    {
        $email = $this->getValue($key);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError($key, 'email');
        }
        return $this;
    }
}

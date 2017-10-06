<?php

namespace Framework\Validator;

class ValidationError
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $rule;

    /**
     * @var array
     */
    private $messages = [
        'required' => 'Field %s is required',
        'empty' => 'Field %s can\'t be empty',
        'slug' => 'Field %s is not a valid slug',
        'minLength' => 'Field %s should be longer than %d caracteres',
        'maxLength' => 'Field %s have to be shorter than %d caracteres',
        'betweenLength' => 'Field %s need to be in range length %d et %d',
        'dateTime' => 'Field %s is not valid date',
        'exists' => 'Field %s does not exist in table %S',
        'unique' => 'Field %s need to be unique',
        'filetype' => 'Field %s is not format : (%s)',
        'uploaded' => 'File Upload is required',
        'email' => '%s is not a valid email',
    ];
    /**
     * @var array
     */
    private $attributes;

    /**
     * ValidationError constructor.
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return call_user_func_array('sprintf', $params);
    }
}

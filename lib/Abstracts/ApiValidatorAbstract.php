<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 19:10
 *
 */


namespace WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts;


abstract class ApiValidatorAbstract
{
    abstract function rules(): array;

    private $errorMessages = [];
    private $ruleDelimiter = ':';

    protected function validate(): array
    {
        foreach ($this->rules() as $key => $rules) {
            foreach ($rules as $rule) {
                if ($pos = strpos($rule, ':')) {
                    $paramStr = substr($rule, $pos + 1);
                    $ruleFnc = substr($rule, 0, $pos);
                    if (!empty($error = $this->$ruleFnc($key, $paramStr))) {
                        $this->errorMessages[$key][] = $error;
                    }
                } else {
                    if (!empty($error = $this->$rule($key))) {
                        $this->errorMessages[$key][] = $error;
                    }
                }
            }
        }

        return $this->errorMessages;
    }

    function in(string $key, string $param): ?string
    {
        if (!array_key_exists($key, $_REQUEST)) {
            return sprintf('%s is not %s', $key, $param);
        }

        if (array_key_exists($_REQUEST[$key], array_flip(explode(',', $param)))) {
            return null;
        } else {
            return sprintf('%s is not %s', $key, $param);
        }
    }

    function required($key): ?string
    {
        if (array_key_exists($key, $_REQUEST)) {
            return null;
        }

        return sprintf('error parameter %s not faund', $key);
    }
}
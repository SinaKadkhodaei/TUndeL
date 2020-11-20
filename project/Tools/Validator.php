<?php

namespace Tools;

use Exception;

class Validator
{
	public static function check($_data, $_conds)
	{
		$errors = [];
		foreach ($_conds as $key => $cond) {
			$required = $key[0];
			$cast = $key;

			if ($required === '!') {
				$cast = $key = substr($key, 1);
				if (isset($cond['cast']))
					$cast = $cond['cast'];

				if (is_numeric($_data[$key] ?? false))
					settype($_data[$key], TypeInt);

				if (!isset($_data[$key]) || (!in_array(gettype($_data[$key]), ['boolean', 'integer']) && empty($_data[$key]))) {
					$errors[] = __ValidatorErrors::public('required', ['cast' => $cast]);
					continue;
				}
			} elseif (!isset($_data[$key]))
				continue;
			else {
				if (is_numeric($_data[$key]))
					settype($_data[$key], TypeInt);

				if (isset($cond['cast']))
					$cast = $cond['cast'];
			}

			if (isset($cond['type']))
				switch ($cond['type']) {
					case TypeArray:
						if (!is_array($_data[$key]))
							$errors[] = __ValidatorErrors::array('isArray', ['cast' => $cast]);
						break;
					case TypeString:
						if (!(is_string($_data[$key]) || is_numeric($_data[$key])))
							$errors[] = __ValidatorErrors::string('isString', ['cast' => $cast]);

						if (isset($cond['regex']))
							if (!preg_match($cond['regex']['pattern'], $_data[$key]))
								$errors[] =  __ValidatorErrors::string('regex', ['cast' => $cast, 'pattern' => $cond['regex']['pattern'], 'format' => $cond['regex']['format']]);
						if (isset($cond['maxLen']))
							if (strlen($_data[$key]) > $cond['maxLen'])
								$errors[] =  __ValidatorErrors::string('maxLen', ['cast' => $cast, 'maxLen' => $cond['maxLen']]);
						if (isset($cond['minLen']))
							if (strlen($_data[$key]) < $cond['minLen'])
								$errors[] =  __ValidatorErrors::string('minLen', ['cast' => $cast, 'minLen' => $cond['minLen']]);
						if (isset($cond['betweenLen']))
							if (strlen($_data[$key]) < $cond['betweenLen'][0] || strlen($_data[$key]) > $cond['betweenLen'][1])
								$errors[] =  __ValidatorErrors::string('betweenLen', ['cast' => $cast, 'minLen' => $cond['betweenLen'][0], 'maxLen' => $cond['betweenLen'][1]]);
						if (isset($cond['len']))
							if (strlen($_data[$key]) !== $cond['len'])
								$errors[] = __ValidatorErrors::string('len', ['cast' => $cast, 'minLen' => $cond['betweenLen'][0], 'maxLen' => $cond['betweenLen'][1]]);
						break;
					case TypeInt:
						if (!is_numeric($_data[$key]))
							$errors[] = __ValidatorErrors::int('isNumber', ['cast' => $cast]);

						if (isset($cond['max']))
							if ($_data[$key] > $cond['max'])
								$errors[] = __ValidatorErrors::int('max', ['cast' => $cast, 'max' => $cond['max']]);
						if (isset($cond['min']))
							if ($_data[$key] < $cond['min'])
								$errors[] = __ValidatorErrors::int('min', ['cast' => $cast, 'min' => $cond['min']]);
						if (isset($cond['between']))
							if ($_data[$key] < $cond['between'][0] || $_data[$key] > $cond['between'][1])
								$errors[] = __ValidatorErrors::int('between', ['cast' => $cast, 'min' => $cond['between'][0], 'max' => $cond['between'][1]]);
						break;
					case TypeBool:
						if (!is_bool($_data[$key]))
							$errors[] = __ValidatorErrors::bool('isBool', ['cast' => $cast]);
						break;
					case TypeFile:
						if (isset($cond['maxSize'])) // KB
							if ($_data[$key]['size'] > ($cond['maxSize'] * 1000))
								$errors[] = __ValidatorErrors::file('maxSize', ['cast' => $cast, 'maxSize' => $cond['maxSize']]);
						if (isset($cond['minSize']))
							if ($_data[$key]['size'] < ($cond['minSize'] * 1000))
								$errors[] = __ValidatorErrors::file('minSize', ['cast' => $cast, 'minSize' => $cond['minSize']]);
						if (isset($cond['betweenSize']))
							if ($_data[$key]['size'] < ($cond['betweenSize'][0] * 1000) || $_data[$key]['size'] > ($cond['betweenSize'][1] * 1000))
								$errors[] = __ValidatorErrors::file('betweenSize', ['cast' => $cast, 'minSize' => $cond['betweenSize'][0], 'maxSize' => $cond['betweenSize'][1]]);
						if (isset($cond['mime']))
							if (!in_array($_data[$key]['type'], array_keys($cond['mime'])))
								$errors[] = __ValidatorErrors::file('mime', ['cast' => $cast, 'mime' => implode(' ، ', $cond['mime'])]);
						break;
				}
		}

		return [
			'status' => (count($errors) > 0 ? false : true),
			'errors' => $errors
		];
	}
}

class __ValidatorErrors
{
	private $errors = [
		'public' => [
			'required' => 'لطفا @cast را وارد نمایید',
		],
		'array' => [
			'isArray' => 'لطفا مقدار @cast را از نوع آرایه وارد نمایید',
		],
		'string' => [
			'isString' => 'لطفا @cast را مقدار رشته ای وارد نمایید',
			'regex' => 'لطفا @cast را با قالب @format وارد نمایید',
			'maxLen' => 'لطفا @cast را کمتر از @maxLen حرف وارد نمایید',
			'minLen' => 'لطفا @cast را بیشتر از @minLen حرف وارد نمایید',
			'betweenLen' => 'لطفا @cast را بین @minLen و @maxLen حرف وارد نمایید',
			'len' => 'لطفا @cast را @len حرف وارد نمایید',
		],
		'int' => [
			'isNumber' => 'لطفا @cast را مقدار عددی وارد نمایید',
			'max' => 'لطفا @cast را کمتر از @max وارد نمایید',
			'min' => 'لطفا @cast را بیشتر از @min وارد نمایید',
			'between' => 'لطفا @cast را بین @min و @max وارد نمایید',
		],
		'bool' => [
			'isBool' => 'لطفا @cast را مقدار صحیح و غلط وارد نمایید',
		],
		'file' => [
			'maxSize' => 'لطفا حجم @cast را کمتر از @maxSize کیلوبایت وارد نمایید',
			'minSize' => 'لطفا حجم @cast را بیشتر از @minSize کیلوبایت وارد نمایید',
			'betweenSize' => 'لطفا حجم @cast را بین @minSize و @maxSize کیلوبایت وارد نمایید',
			'mime' => 'لطفا نوع @cast را از نوع های @mime وارد نمایید',
		],
	];

	/**
	 * @var __ValidatorErrors $instance
	 */
	private static
		$instance = null;


	public static function __callStatic($_name, $_args)
	{
		if (is_null(self::$instance))
			self::$instance = new self();

		$errorName = $_args[0] ?? null;
		$_args = $_args[1] ?? [];
		$error = self::$instance->errors[$_name][$errorName] ?? null;
		if (is_null($error))
			throw new Exception('there is no error message named ' . $_name);

		return partialContent($error, $_args);
	}
}

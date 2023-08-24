<?php

namespace app\forms;

use yii\base\Model;

class ProxyForm extends Model
{
    private const PATTERN = '/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?=[^\d])\s*:?\s*(\d{2,5})/m';

    public string $proxyInput = '';
    private string $invalidProxy;
    private array $parsedProxy;

    public function rules(): array
    {
        return [
            ['proxyInput', 'required'],
            ['proxyInput', 'string'],
            ['proxyInput', 'proxyValidator'],
        ];
    }

    /**
     * Фильтр по регулярке
     * @param string $line
     * @return bool
     */
    private function filterLine(string $line): bool
    {
        return (bool)preg_match(self::PATTERN, $line);
    }

    /**
     * Парсим прокси в массив и проверяем регуляркой
     * @return bool
     */
    private function checkProxyArray(): bool
    {
        $this->parsedProxy = explode(PHP_EOL, $this->proxyInput);
        foreach ($this->parsedProxy as $item) {
            if (!$this->filterLine($item)) {
                $this->invalidProxy = $item;
                return false;
            }
        }
        return true;
    }

    /**
     * Функция-валидатор для поля с прокси
     * @param $attribute
     * @param $params
     * @param $validator
     * @return void
     */
    public function proxyValidator($attribute, $params, $validator): void
    {
        if (!$this->checkProxyArray()) {
            $this->addError($attribute, 'Обнаружен невалидный прокси во входных данных - ' . $this->invalidProxy);
        }
    }

    public function attributeLabels(): array
    {
        return [
            'proxyInput' => 'Список прокси'
        ];
    }

    /**
     * @return array
     */
    public function getParsedProxy(): array
    {
        return $this->parsedProxy;
    }
}
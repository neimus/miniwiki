<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.18
 */

namespace app\components;

use yii\i18n\Formatter;

class PageFormatter extends Formatter
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     *
     * @return string
     */
    public function asWikiStyle(string $value): string
    {
        $this->value = $value;

        $this->formatBold()
            ->formatItalics()
            ->formatUri();

        return $this->value;
    }

    /**
     * @return $this
     */
    private function formatBold(): self
    {
        $this->value = preg_replace('%[\*]{2}([^\s].*[^\s])[\*]{2}%', '<b>$1</b>', $this->value);

        return $this;
    }

    /**
     * @return $this
     */
    private function formatItalics(): self
    {
        $this->value = preg_replace('%[\\\]{2}([^\s].*[^\s])[\\\]{2}%', '<i>$1</i>', $this->value);

        return $this;
    }

    /**
     * @return $this
     */
    private function formatUri(): self
    {
        $this->value = preg_replace('%[\(]{2}/?([\w+/]+)[\s]([^\s][^\)\)\n\r\t]+[^\s])[\)]{2}%', '<a href="/$1">$2</a>',
            $this->value);

        return $this;
    }
}
<?php

namespace Ap\I18n\Translator;

class Translator extends \Zend\I18n\Translator\Translator
{
    public function getAllMessages($textDomain = 'default', $locale = null)
    {
        $locale = $locale ?: $this->getLocale();
        if (!isset($this->messages[$textDomain][$locale])) {
            $this->loadMessages($textDomain, $locale);
        }
        return $this->messages[$textDomain][$locale];
    }
}
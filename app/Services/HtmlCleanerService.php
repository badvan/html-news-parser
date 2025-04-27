<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlCleanerService extends AbstractSiteService
{
    public function clean(string $html): string
    {
        $purifier = new HTMLPurifier($this->getConfig());

        return $purifier->purify($html);
    }

    /**
     * Создаёт и возвращает конфигурацию для HTMLPurifier
     * Find full HTML5 config : https://github.com/kennberg/php-htmlpurfier-html5
     *
     * @return HTMLPurifier_Config Конфигурация HTMLPurifier
     */
    private function getConfig(): HTMLPurifier_Config
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('URI.Base', $this->getBaseUrl());
        $config->set('URI.MakeAbsolute', true);
        $config->set('Cache.SerializerPath', '/tmp');
        //$config->set('Attr.EnableID', true);
        //$config->set('Attr.IDPrefix', md5(uniqid(rand(), true)));

        // Allow iframes from:
        // o YouTube.com
        // o Vimeo.com
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

        // Разрешаем необходимые элементы
        $config->set('HTML.AllowedElements', 'header, section, article, nav, aside, footer, video, source, h1, h2, h3, h4, h5, h6, div, p, b, strong, em, a, ul, li, ol, img');

        // Разрешаем атрибуты для элементов
        $config->set('HTML.AllowedAttributes', 'class, a.href, img.src, video.src, video.poster, video.width, video.height, video.controls, video.preload, source.src, source.type');

        //$config->set('HTML.Allowed', 'video[poster|preload|controls|width|height|src], source[src|type]');

        //$config->set('HTML.Allowed', 'header[class],section[class],article[class],nav[class],aside[class],footer[class],h1[class],h2[class],h3[class],h4[class],h5[class],h6[class],video[poster|preload|controls|width|height|src],source[src|type],div[class],span,p,b,a[href],img[src],ul,li,ol');

        // Определяем уникальный ID и ревизию для определения HTML5
        $config->set('HTML.DefinitionID', 'html5-definitions' . md5('salt_01')); // уникальный ID
        $config->set('HTML.DefinitionRev', 1);
        $config->set('Cache.DefinitionImpl', null); // TODO: удалить это позже!

        // Добавляем поддержку элементов видео и источника, если возможно
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('nav', 'Block', 'Flow', 'Common');
            $def->addElement('article', 'Block', 'Flow', 'Common');
            $def->addElement('aside', 'Block', 'Flow', 'Common');
            $def->addElement('header', 'Block', 'Flow', 'Common');
            $def->addElement('footer', 'Block', 'Flow', 'Common');

            // Разрешаем <video> с нужными атрибутами
            // http://developers.whatwg.org/the-video-element.html#the-video-element
            $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                'src' => 'URI',
                'type' => 'Text',
                'width' => 'Length',
                'height' => 'Length',
                'poster' => 'URI',
                'preload' => 'Enum#auto,metadata,none',
                'controls' => 'Bool',
            ]);

            // Разрешаем <source> внутри <video> с атрибутами
            $def->addElement('source', 'Block', 'Flow', 'Common', [
                'src' => 'URI',
                'type' => 'Text',
            ]);
        }

        return $config;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jskoczek
 * Date: 28/08/18
 * Time: 12:34
 */

namespace toTwig\Converter;

use toTwig\ConverterAbstract;

class SectionConverter extends ConverterAbstract
{

    /**
     * Function converts smarty {section} tags to twig {for}
     *
     * @param \SplFileInfo $file
     * @param string $content
     * @return null|string|string[]
     */
    public function convert(\SplFileInfo $file, $content)
    {
        $contentReplacedOpeningTag = $this->replaceSectionOpeningTag($content);
        $content = $this->replaceSectionClosingTag($contentReplacedOpeningTag);

        return $content;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'section';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Convert smarty {section} to twig {for}';
    }

    /**
     * Function converts opening tag of smarty {section} to twig {for}
     *
     * @param $content
     * @return null|string|string[]
     */
    private function replaceSectionOpeningTag($content)
    {
        $pattern = '/\[\{section\b\s*([^{}]+)?\}\]/';
        $string = '{% for :name in :start..:loop %}';

        return preg_replace_callback($pattern, function($matches) use ($string) {

            $match = $matches[1];
            $search = $matches[0];

            $attr = $this->attributes($match);
            if(!isset($attr['start'])) {
                $attr['start'] = 0;
            }
            $replace = $attr;
            $string = $this->vsprintf($string, $replace);

            // Replace more than one space to single space
            $string = preg_replace('!\s+!', ' ', $string);

            return str_replace($search, $string, $search);

        }, $content);
    }

    /**
     * Function converts closing tag of smarty {section} to twig {for}
     *
     * @param $content
     * @return null|string|string[]
     */
    private function replaceSectionClosingTag($content)
    {
        $search = '#\[\{/section\s*\}\]#';
        $replace = '{% endfor %}';
        return preg_replace($search, $replace, $content);
    }
}

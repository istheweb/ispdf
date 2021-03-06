<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 23/01/17
 * Time: 14:12
 */

namespace istheweb\ispdf\classes;

use Barryvdh\DomPDF\PDF as LaravelPDF;
use Istheweb\IsPdf\Models\Layout;
use Istheweb\IsPdf\Models\Template;
use Twig;

/**
 * Class PDFWrapper
 * @package istheweb\ispdf\classes
 */
class PDFWrapper extends LaravelPDF
{

    /**
     * Load template HTML
     *
     * @param string $code
     * @param array $data
     * @param null $encoding
     * @return $this
     */
    public function loadTemplate($code, $data = [], $encoding = null)
    {
        $this->loadHTML(
            $this->parseTemplate(Template::byCode($code), $data),
            $encoding
        );

        return $this;
    }

    /**
     * Load layout HTML
     *
     * @param string $code
     * @param array $data
     * @param null $encoding
     * @return $this
     */
    public function loadLayout($code, $data = [], $encoding = null)
    {
        $this->loadHTML(
            $this->parseLayout(Layout::byCode($code), $data),
            $encoding
        );

        return $this;
    }

    /**
     * Get parsed HTML from template
     *
     * @param $template
     * @param array $data
     * @return mixed
     */
    public function parseTemplate($template, $data = [])
    {
        $html = Twig::parse($template->content_html, $data);

        if ( ! $template->layout) {
            return $html;
        }

        return $this->parseLayout(
            $template->layout,
            array_merge(['content_html' => $html], $data)
        );
    }

    /**
     * Get parsed HTML from layout
     *
     * @param $layout
     * @param array $mergeData
     * @return mixed
     */
    public function parseLayout($layout, $mergeData = [])
    {
        return Twig::parse(
            $layout->content_html,
            $this->layoutData($layout, $mergeData)
        );
    }

    /**
     * @param $layout
     * @param $mergeData
     * @return array
     */
    protected function layoutData($layout, $mergeData)
    {
        return array_merge(
            [
                'background_img' => $layout->background_img ? $layout->background_img->getPath() : null,
                'css' => $layout->content_css
            ],
            $mergeData
        );
    }

}
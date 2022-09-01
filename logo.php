<?php

require __DIR__ . '/vendor/autoload.php';
require_once './CsvHelper.php';

//use EasySVG;
use SVG\SVG;
use SVG\Nodes\Structures\SVGFont;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Shapes\SVGPath;
use SVG\Nodes\Shapes\SVGRect;

class SvgGenerator {


    /** @var SVG  */
    private $svg = null;
    private $svgWidth = 2000;
    private $svgHeight = 178;
    private $textCorrection = 6;
    private $logoTextPadding = 50;
    private $textLeft = 'CEVI';
    private $textRight = 'SCHWEIZ';
    private $showIcon = true;
    private $font = null;
    private $color = null;
    private $colorWhite = '#FFFFFF';
    private $fontSvgPath = __DIR__ . '/assets/fonts/montserrat/Montserrat-ExtraBold.svg';

    private $CsvHelper = null;

    private $colorPrint = [
        'black' => '#000000',
        'blue' => '#0026ff',
        'red' => '#ff0033'
    ];

    private $colorWeb = [
        'black' => '#141412',
        'blue' => '#323394',
        'red' => '#c41333'
    ];

    private $colorBlack = [
        'black' => '#141412',
        'blue' => '#141412',
        'red' => '#141412'
    ];

    private function getTextDimension($text, $svg) {
        $entry = $this->CsvHelper->getSizeEntry($text);

        if (is_array($entry)) {
            return [
                'width' => $entry[1],
                'height' => null
            ];
        }

        $im = new Imagick();
        $im->readImageBlob($svg);
        $im->trimImage(0);
        $dimension = $im->getImageGeometry();

        $this->CsvHelper->saveSizeEntry($text, $dimension['width'] - 2);

        return $dimension;
    }

    /**
     * Get the path out of a svg with a text-path.
     *
     * @param EasySVG $svg
     * @return string
     */
    private function getPathOutOfSvg(EasySVG $svg) {
        $xml = $svg->asXML();
        $removeXml = preg_replace('/^<\\?.*\\?>$/m', '', $xml);
        $removeLineBreaks = preg_replace('/\n/m', '', $removeXml);
        $removeSvgStart = preg_replace('/^<svg.*\">/m', '', $removeLineBreaks);
        $removeSvgEnd = preg_replace('/<\/svg>/m', '', $removeSvgStart);
        $removePathStart = preg_replace('/<path\sd="/m', '', $removeSvgEnd);
        $removePathEnd = preg_replace('/"\/>/m', '', $removePathStart);
        return $removePathEnd;
    }

    private function strtoupper_utf8($str) {
        $from = array('ä','ö','ü');
        $to = array('Ä','Ö','Ü');

        return strtoupper(str_replace($from, $to, $str));
    }

    /**
     * Remove bad text from $text.
     * Has a whitelist-function for good words.
     *
     * @param $text
     * @return string good text
     * @throws Exception
     */
    private function removeBadText($text) {
        $tmpText = $this->strtoupper_utf8($text);

        $badWords = [
            'pfadi',
            'besj',
            'pfadfinder',
            'jungwacht',
            'jubla',
            'blauring',
            'blaues kreuz',
            'pro natura',
            'naturfreunde',
            'wölfli',
            'fünkli',
            'jublini',
            'bienli',
            'kadetten',
            'sajv',
            'rover',
            'piss',
            'scheisse',
            'shit',
            'fuck',
            'ficken',
            'fick',
            'penis',
            'pimmel',
            'arschloch',
            'fotze',
            'schlampe',
            'bitch',
            'nutte',
            'titten'
        ];

        for ($i = 0; $i < sizeof($badWords); $i += 1) {
            $tmpText = preg_replace(
                '/' . $this->strtoupper_utf8($badWords[$i]) . '/i',
                '---',
                $tmpText
            );
        }

        /**
         * #11 defuse bad words.
         * @see https://github.com/cevi/logo-generator/issues/11 */
        $blackList = [
            'arsch'
        ];
        $whiteList = [
            'marsch'
        ];

        if (sizeof($blackList) !== sizeof($whiteList)) {
            throw new Exception('blacklist words and whitelist words have not equal length, they must match together.');
        }

        for ($i = 0; $i < sizeof($blackList); $i += 1) {
            $tmpText = preg_replace(
                '/' . $this->strtoupper_utf8($blackList[$i]) . '(?<!' . $this->strtoupper_utf8($whiteList[$i]) . ')/i',
                '---',
                $tmpText
            );
        }

        return $tmpText;
    }

    /**
     * Generate the text as a SVG-Text, add the SVG as node to the SVG.
     *
     * @param $text
     * @param $size
     * @param $x
     * @param $y
     * @return array (text: node, width: width of the text, height: height of the text)
     * @throws Exception
     */
    private function generateText($text, $size, $x, $y) {
        try {
            $correctedText = $this->removeBadText($text);
        } catch (Exception $e) {
            $correctedText = $text;
        }

        $textSvg = new EasySVG();
        $textSvg->addAttribute('width', $this->svgWidth . 'px');
        $textSvg->addAttribute('height', $this->svgHeight . 'px');
        $textSvg->setFont($this->fontSvgPath, $size);
        $textSvg->addText($correctedText, $x, $y);

        $textSvgPath = $this->getPathOutOfSvg($textSvg);
        $textSvgPathElement = new SVGPath($textSvgPath);

        $this->svg->getDocument()->addChild($textSvgPathElement);

        $dimension = $this->getTextDimension($correctedText, $textSvg->asXML());

        $textNode = $this->svg->getDocument()->getChild($this->svg->getDocument()->countChildren() - 1);

        return [
            'text' => $textNode,
            'width' => $dimension['width'] - 2,
            'height' => $dimension['height'] - 2,
        ];
    }

    /**
     * Add the Logo-Icon at position $x / $y
     * @param $xDecimal
     * @param $yDecimal
     */
    private function addLogoIcon($xDecimal, $yDecimal) {
        $x = round($xDecimal);
        $y = round($yDecimal);

        $blue1 = new SVGPath('M' . $x . '.931,' . ($y + 36) . '.371c-0.181-0.641-0.267-1.301-0.327-1.85l12.602,12.581 C' . ($x + 6) . '.397,' . ($y + 44) . '.64,' . ($x + 2) . '.158,' . ($y + 44) . '.975,' . $x . '.931,' . ($y + 36) . '.371');
        $blue2 = new SVGPath('M' . ($x + 126) . '.756,' . ($y + 49) . '.589c-8.896,10.981-23.162,18.734-46.193,25.432c-23.495,6.697-46.936,6.516-59.736-0.58 c-4.496-2.516-7.312-5.601-8.392-9.293l-6.739-16.765c0.268,0.129,0.535,0.267,0.766,0.5c13.803,7.585,39.672,7.634,65.947,0.128 c21.021-6.073,43.057-18.351,54.348-30.131V' . ($y + 49) . '.589');
        $blue3 = new SVGPath('M' . ($x + 86) . '.924,' . $y . '.621l-82.082,21.879c8.453-12.008,28.283-23.847,51.221-30.49 c6.447-1.871,20.849-4.191,30.861-3.807V' . $y . '.621');
        $red1 = new SVGPath('M' . ($x + 81) . '.722,' . ($y + 78) . '.954c-8.831,2.554-17.685,4.158-26.022,4.703l31.227,31.223l11.095-41.367 C' . ($x + 93) . '.111,' . ($y + 75) . '.417,' . ($x + 87) . '.689,' . ($y + 77) . '.222,' . ($x + 81) . '.722,' . ($y + 78) . '.954');
        $red2 = new SVGPath('M' . ($x + 109) . '.51,' . ($y + 27) .'.399c-11.379,7.401-25.311,13.781-38.275,17.489c-17.806,5.161-35.959,6.776-49.823,4.384 l-21.157-21.185l117.507-31.565L' . ($x + 109) . '.51,' . ($y + 27) . '.399');

        $blue1->setAttribute('fill', $this->color['blue']);
        $blue2->setAttribute('fill', $this->color['blue']);
        $blue3->setAttribute('fill', $this->color['blue']);
        $red1->setAttribute('fill', $this->color['red']);
        $red2->setAttribute('fill', $this->color['red']);

        $group = new SVGGroup();
        $group->addChild($blue1);
        $group->addChild($blue2);
        $group->addChild($blue3);
        $group->addChild($red1);
        $group->addChild($red2);

        $this->svg->getDocument()->addChild($group);
    }

    private function addTextAndIcon() {
        if (!$this->textRight) {
            $this->showIcon = false;
        }

        $xStartTextLeft = $this->logoTextPadding;
        $y = -4.5;
        $logoTextLeft = $this->generateText($this->textLeft, 110, $xStartTextLeft, $y);

        $logoWidth = $this->logoTextPadding * 2 + $logoTextLeft['width'];

        if ($this->showIcon) {
            $xStartIcon = $this->logoTextPadding + $logoTextLeft['width'] + ($this->logoTextPadding / 2) + $this->textCorrection;
            $this->addLogoIcon($xStartIcon, 37);
            $iconWidth = 127;

            $xStartTextRight = $this->logoTextPadding + $logoTextLeft['width'] + $iconWidth + $this->logoTextPadding + $this->textCorrection / 2;
            $logoTextRight = $this->generateText($this->textRight, 110, $xStartTextRight, $y);

            $logoWidth += $this->logoTextPadding + $iconWidth + $logoTextRight['width'] + $this->textCorrection * 2;
        }

        // update: change this when the slogan will be part of the svg.
        $this->svgWidth = $logoWidth;
        $this->svg->getDocument()->setWidth($logoWidth);

        // todo: add second line for the right text
    }

    private function addBackground() {
        $background = new SVGRect(0, 0, $this->svgWidth, $this->svgHeight);
        $background->setAttribute('fill', $this->colorWhite);
        $this->svg->getDocument()->addChild($background, 1);
    }

    private function prepareSvg() {
        $this->svg = new SVG($this->svgWidth, $this->svgHeight);
        $this->font = new SVGFont('montBold', '/assets/fonts/montserrat/Montserrat-ExtraBold.ttf');
        $this->svg->getDocument()->addChild($this->font);
    }

    public function __construct($textLeft = null, $textRight = null, $color = null) {
        $this->CsvHelper = new CsvHelper();

        if ($textLeft) { $this->textLeft = $textLeft; }
        if ($textRight) { $this->textRight = $textRight; }

        // color=[web|print|black]
        $this->color = $this->colorWeb;

        if ($color == 'print') {
            $this->color = $this->colorPrint;
        }
        else if ($color == 'black') {
            $this->color = $this->colorBlack;
        }

        $this->prepareSvg();
        $this->addTextAndIcon();
        $this->addBackground();
    }

    public function outputSvg() {
        header('Content-Type: image/svg+xml');
        echo $this->svg;
    }
}

$left = null;
$right = null;
$color = null;

if (isset($_GET['left'])) { $left = $_GET['left']; }
if (isset($_GET['right'])) { $right = $_GET['right']; }

// color=[web|print|black]
if (isset($_GET['color'])) { $color = $_GET['color']; }

$svgGenerator = new SvgGenerator($left, $right, $color);

// print svg
$svgGenerator->outputSvg();

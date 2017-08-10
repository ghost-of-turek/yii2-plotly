<?php

/**
 * @copyright Copyright &copy; Artur Ciesielski, 2017
 * @package yii2-plotly
 * @version 1.0.0
 */

namespace turek\plotly;

use turek\plotly\PlotlyAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Plotly widget is a Yii2 wrapper for Plotly.js. It is a library for client-side rendering
 * of plots and charts. This widget enables easy integration with any Yii2 based application.

 * @author Artur Ciesielski <artur.ciesielski@gmail.com>
 * @see https://plot.ly/javascript/
 */
class Plotly extends Widget
{
    public const
        SOURCE_TYPE_CSV = 'csv',
        SOURCE_TYPE_JSON = 'json';

    public const
        GRAPH_TYPE_AREA = 'area',
        GRAPH_TYPE_BAR = 'bar',
        GRAPH_TYPE_BOX = 'box',
        GRAPH_TYPE_CHOROPLETH = 'choropleth',
        GRAPH_TYPE_CONTOUR = 'contour',
        GRAPH_TYPE_HEATMAP = 'heatmap',
        GRAPH_TYPE_HISTOGRAM = 'histogram',
        GRAPH_TYPE_HISTOGRAM_2D = 'histogram2d',
        GRAPH_TYPE_HISTOGRAM_2D_CONTOUR = 'histogram2dcontour',
        GRAPH_TYPE_MESH_3D = 'mesh3d',
        GRAPH_TYPE_PIE = 'pie',
        GRAPH_TYPE_SCATTER = 'scatter',
        GRAPH_TYPE_SCATTER_3D = 'scatter3d',
        GRAPH_TYPE_SCATTER_GEO = 'scattergeo',
        GRAPH_TYPE_SCATTER_GL = 'scattergl',
        GRAPH_TYPE_SURFACE = 'surface';

    public const
        BARMODE_STACK = 'stack',
        BARMODE_OVERLAY = 'overlay',
        BARMODE_RELATIVE = 'relative',
        BARMODE_GROUP = 'group',
        BARMODE_OFFSET = 'offset';

    /**
     * Type of source file to be parsed. One of: "csv" | "json".
     *
     * @var string
     */
    public $sourceType;

    /**
     * Link to source file (CSV or JSON) with input data. Can be specified as a remote resource.
     * Can be omitted if the $traces field already has the necessary data embedded.
     *
     * @var string
     */
    public $sourceFile;

    /**
     * Configuration arrays for traces. Please refer to Plotly's documentation for field reference.
     * For fields specified as callbacks, the signature should be 'function (data) { ... }'.
     *
     * @var array[]
     */
    public $traces = [];

    /**
     * Configuration array for layout. Please refer to Plotly's documentation for field reference.
     *
     * @var array
     * @see https://plot.ly/javascript/reference/
     */
    public $layout = [];

    /**
     * HTML options for Plotly's container.
     *
     * @var array
     */
    public $options = [];

    /**
     * Options passed to Plotly during graph rendering.
     *
     * @var array
     */
    public $chartOptions = [];

    /**
     * Should the animated loading bars be rendered.
     *
     * @var bool
     */
    public $loadingAnimation = true;

    /**
     * Functions that will be available for data formatting (array in the form of ['functionName' => JS function]).
     * If you would like to have certain functions available in every Plotly widget consider configuring the Dependency Injector.
     *
     * @var string[]
     */
    public $formatters = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (isset($this->sourceFile) && !isset($this->sourceType)) {
            throw new InvalidConfigException('Property Plotly::$sourceType must be set to one of "csv" or "json" when Plotly::$sourceFile is set.');
        }

        $this->registerTranslations();
        PlotlyAsset::register($this->view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $htmlOptions = $this->options;
        $htmlOptions['id'] = $this->getId();

        $content = $this->loadingAnimation ? $this->getAnimatedBarsContent() : '';

        echo Html::tag('div', $content, $htmlOptions);
        $this->view->registerJs(new JsExpression($this->getClientScript()), View::POS_END);
    }

    private function getClientScript() : string
    {
        $traces = Json::htmlEncode($this->traces);
        $layout = Json::htmlEncode($this->layout);
        $chartOptions = Json::htmlEncode($this->chartOptions);
        $afterPlotScript = $this->loadingAnimation ? ".then(function() { $('#{$this->getId()} > .plotlybars-wrapper').remove(); })" : '';

        $js = '';
        foreach ($this->formatters as $formatterName => $formatterDefinition) {
            $js .= "var {$formatterName} = {$formatterDefinition};";
        }

        if ($this->sourceFile) {
            $js .= "Plotly.d3.{$this->sourceType}(\"{$this->sourceFile}\", function (data) {
                var traces = $traces;
                var layout = $layout;

                Plotly.plot(document.getElementById(\"{$this->getId()}\"), traces, layout, {$chartOptions}){$afterPlotScript};
            });";
        } else {
            $js .= "
                var traces = $traces;
                var layout = $layout;
                Plotly.plot(document.getElementById(\"{$this->getId()}\"), traces, layout, {$chartOptions}){$afterPlotScript};
            ";
        }

        return $js;
    }

    private function getAnimatedBarsContent() : string
    {
        return
            '<div class="plotlybars-wrapper">
                <div class="plotlybars-content">
                    <div class="plotlybars">
                        <div class="plotlybars-bar b1"></div>
                        <div class="plotlybars-bar b2"></div>
                        <div class="plotlybars-bar b3"></div>
                        <div class="plotlybars-bar b4"></div>
                        <div class="plotlybars-bar b5"></div>
                        <div class="plotlybars-bar b6"></div>
                        <div class="plotlybars-bar b7"></div>
                    </div>
                    <div class="plotlybars-text">
                        ' . mb_strtolower(Yii::t('plotly', 'Loading')) . '...
                    </div>
                </div>
            </div>';
    }

    private function registerTranslations(string $category = null)
    {
        $category = $category ?? 'plotly';
        Yii::setAlias($category, __DIR__);

        $config = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => "@{$category}/messages",
            'forceTranslation' => true,
        ];

        Yii::$app->i18n->translations["{$category}*"] = $config;
    }
}

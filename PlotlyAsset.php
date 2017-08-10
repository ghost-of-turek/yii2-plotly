<?php

/**
 * @copyright Copyright &copy; Artur Ciesielski, 2017
 * @package yii2-plotly
 * @version 1.0.0
 */

namespace turek\plotly;

use yii\web\AssetBundle;

/**
 * Asset bundle for [[Plotly]] Widget
 *
 * @author Artur Ciesielski <artur.ciesielski@gmail.com>
 */
class PlotlyAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->basePath = '@web/assets';

        $this->css = ['css/plotly.css'];
        $this->js = ['js/plotly-1.29.3.min.js'];

        parent::init();
    }
}

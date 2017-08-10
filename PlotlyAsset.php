<?php

/**
 * @copyright Copyright &copy; Artur Ciesielski, 2017
 * @package yii2-plotly
 * @version 1.0.0
 */

namespace turek\plotly;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

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
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/plotly']);
        $this->setupAssets('js', ['js/plotly-1.29.3']);
        parent::init();
    }
}

<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-15 09:25
 */

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class Bar extends Widget
{
    public $buttons = [];

    public $options = [
        'class' => 'mail-tools tooltip-demo m-t-md',
    ];
    public $template = "{refresh} {create} {delete} ";


    /**
     * @inheritdoc
     */
    public function run()
    {
        $buttons = '';
        $this->initDefaultButtons();
        $buttons .= $this->renderDataCellContent();
        return "<div class='{$this->options['class']}'>{$buttons}</div>";
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent()
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                return $this->buttons[$name] instanceof \Closure ? call_user_func($this->buttons[$name]) : $this->buttons[$name];
            } else {
                return '';
            }


        }, $this->template);
    }

    /**
     * 生成默认按钮
     *
     */
    protected function initDefaultButtons()
    {
        if (! isset($this->buttons['refresh'])) {
            $this->buttons['refresh'] = function () {
                return Html::a('<i class="fa fa-refresh"></i> ' . Yii::t('app', 'Refresh'), Url::to(['refresh']), [
                    'title' => Yii::t('app', 'Refresh'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-white btn-sm refresh',
                ]);
            };
        }

        if (! isset($this->buttons['create'])) {
            $this->buttons['create'] = function () {
                return Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), Url::to(['create']), [
                    'title' => Yii::t('app', 'Create'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-white btn-sm',
                ]);
            };
        }

        if (! isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function () {
                return Html::a('<i class="fa fa-trash-o"></i> ' . Yii::t('app', 'Delete'), Url::to(['delete']), [
                    'title' => Yii::t('app', 'Delete'),
                    'data-pjax' => '0',
                    'data-confirm' => Yii::t('app', 'Really to delete?'),
                    'class' => 'btn btn-white btn-sm multi-operate',
                ]);
            };
        }

        if (! isset($this->buttons['export'])) {
            $this->buttons['export'] = function () {
                return Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Export'), Url::to(['export','data'=>Yii::$app->request->get()]), [
                    'title' => Yii::t('app', 'Export'),
                    'data-pjax' => '0',
                    'target'=>'_blank',
//                    'data-confirm' => '当且仅当筛选类型为【抽成】时，导出文件后会自动削减对应用户余额，并插入一条提现成功的数据？',
                    'class' => 'btn btn-white btn-sm ',
                ]);
            };
        }

        if (! isset($this->buttons['export_reduce'])) {
            $this->buttons['export_reduce'] = function () {
                return Html::a('<i class="fa fa-download"></i>导出并计算提现', Url::to(['export','data'=>Yii::$app->request->get(),'reduce'=>'reduce']), [
                    'title' => "当且仅当筛选条件包含类型=抽成时，导出文件后会自动计算提现数据",
                    'data-pjax' => '0',
                    'target'=>'_blank',
//                    'data-confirm' => '',
                    'class' => 'btn btn-white btn-sm ',
                ]);
            };
        }

    }
}
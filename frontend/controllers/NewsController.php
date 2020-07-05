<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/4
 * Time: 16:33
 */

namespace frontend\controllers;


use common\models\Article;
use common\models\ArticleContent;
use common\models\meta\ArticleMetaLike;

class NewsController extends BaseController
{
    /**
     * @return array
     */
    public function actionIndex()
    {
        $page = \Yii::$app->request->get('page', 1);
        $pageSize = \Yii::$app->request->get('pageSize', 10);
        $model = Article::find()->select(['id', 'title', 'sub_title', 'summary', 'thumb', 'scan_count', 'author_name'])
            ->where(['status' => 1, 'type' => Article::ARTICLE]);
        $lists = $model->limit($pageSize)->offset($pageSize * ($page - 1))->orderBy('sort desc')->asArray()->all();
        $likeCount = new ArticleMetaLike();
        foreach ($lists as &$item) {
            $item['likeCount'] = $likeCount->getLikeCount($item['id']);
        }
        return $this->outPut([
            'list' => $lists,
            'count' => $model->count()
        ]);

    }

    /**
     * @return array
     */
    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $news = Article::find()->select(['id', 'title', 'sub_title', 'summary', 'thumb', 'scan_count', 'author_name'])->where(['id' => $id]);
        if ($news) {
            $news = $news->asArray()->one();
            $news['likeCount'] = (new ArticleMetaLike())->getLikeCount($news['id']);
            $news['content'] = ArticleContent::findOne(['aid' => $news['id']])->toArray()['content'];
        }
        return $this->outPut($news ? $news : []);
    }
}
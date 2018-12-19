# yii2-external-crud

Controller
------------------------------
```php
public function actions()
{
	return [
		...
		'create' => [
            'class' => 'ivankff\yii2ExternalCrud\actions\CreateAction',
            'view' => 'update',
            'additionalQueryParams' => ['F.categoryId'],
            'model' => function ($action) {
                $categoryId = Yii::$app->request->get('categoryId');
                $config = [];
                if ($categoryId)
                    $config = ['categories' => $categoryId];

                $model = new ProductForm(new Product(), $config);
                $model->status = 1;

                if (Yii::$app->user->can('backend.product.webmaster'))
                    $model->setScenario(ProductForm::SCENARIO_WEBMASTER);

                return $model;
            },
            'on ' . WriteAction::EVENT_BEFORE_VIEW => function ($event) {
                /** @var ActionWriteViewEvent $event */
                $event->viewParams['categories'] = $this->getCategories();
            }
		],
        'update' => [
            'class' => 'ivankff\yii2ExternalCrud\actions\UpdateAction',
            'view' => 'update',
            'additionalQueryParams' => ['F.categoryId'],
            'model' => function ($id, $action) {
                $model = new ProductForm($this->findModel($id));

                if (Yii::$app->user->can('backend.product.webmaster'))
                    $model->setScenario(ProductForm::SCENARIO_WEBMASTER);

                return $model;
            },
            'on ' . WriteAction::EVENT_BEFORE_VIEW => function ($event) {
                /** @var ActionWriteViewEvent $event */
                $event->viewParams['categories'] = $this->getCategories();
            }
        ],
        'delete' => [
            'class' => 'ivankff\yii2ExternalCrud\actions\DeleteAction',
            'model' => function ($id, $action) {
                return $this->findModel($id);
            },
        ],
		...
	];
}
```



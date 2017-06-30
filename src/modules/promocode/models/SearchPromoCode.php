<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\promocode\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchPromoСode represents the model behind the search form about `reinvently\ondemand\core\modules\promocode\models\Promocode`.
 */
class SearchPromoCode extends PromoCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'promoType', 'userId', 'amount', 'minAmount', 'usedCount', 'startAt', 'expireAt', 'createdAt', 'updatedAt'], 'integer'],
            [['code', 'days'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Promocode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'promoType' => $this->promoType,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'minAmount' => $this->minAmount,
            'usedCount' => $this->usedCount,
            'startAt' => $this->startAt,
            'expireAt' => $this->expireAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);
            //->andFilterWhere(['like', 'days', $this->days]);

        return $dataProvider;
    }
}

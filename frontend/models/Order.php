<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
	public $addr_id;
	const SCENARIO_API_SUBMIT = "api";
	//这里定义送货方式
	public static $shippingMethod = [
		1=>['顺丰快递',100.00,'速度快,服务好!'],
		2=>['天天快递',50.00,'价格低,速度较慢!'],
		3=>['EMS快递',40.00,'速度慢,但支持全国邮寄!']
	];
	//这里定义支付方式
	public static $paymentMethod = [
		1=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡!'],
		2=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付'],
		3=>['支付宝','支付宝在线支付,方便,安全,快捷!'],
		4=>['微信支付','微信在线支付,方便,安全,快捷!']
	];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
	        [['addr_id', 'delivery_id', 'payment_id', ], 'required','on'=>self::SCENARIO_API_SUBMIT],
	        ['delivery_id','validateDelivery','on'=>self::SCENARIO_API_SUBMIT],
	        ['payment_id','validatePay','on'=>self::SCENARIO_API_SUBMIT],
	        ['addr_id','validateAddr','on'=>self::SCENARIO_API_SUBMIT],
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

	public function validateAddr()
	{
		$model = Address::find()->andWhere(['and',['id' => $this->addr_id],['member_id'=>Yii::$app->user->getId()]])->one();
		if ($model === null) {
			$this->addError('addr_id', '所选地址id不存在');
			return false;
		}
	}
	public function validatePay()
	{
		$exists = array_key_exists($this->payment_id,self::$paymentMethod);
		if (!$exists) {
			$this->addError('payment_id', '所选支付方式id不存在');
			return false;
		}
	}
	public function validateDelivery()
	{
		$exists = array_key_exists($this->delivery_id,self::$shippingMethod);
		if (!$exists) {
			$this->addError('payment_id', '所选送货方式id不存在');
			return false;
		}
	}
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态(0已取消1待付款2待发货3待收货4完成)',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
	        'addr_id' => '送货地址id'
        ];
    }
    public function getOrderGoods(){
    	return $this->hasMany(OrderGoods::className(),['order_id'=>'id']);
    }
}

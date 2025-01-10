<?php
// +----------------------------------------------------------------------
// | Lakala SDK [Lakala SDK for PHP]
// +----------------------------------------------------------------------
// | Lakala SDK
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: endness <endness@qq.com>
// +----------------------------------------------------------------------

namespace endness\lakala\services;

class AggregationCashdesk extends Base
{
    /**
     * 配置参数
     * @var string
     */
    protected $options = [
        // appid
        'appid' => '',
        // 商户证书序列号
        'serial_no' => '',
        // 商户号
        'merc_id' => '',
        // 证书私钥内容
        'private_key' => '',
        // 异步通知验签证书内容
        'certificate' => '',
        // 是否测试环境
        'test_env' => false,
    ];


    /**
     * 收银台订单创建
     * @access public
     * @param string $outOrderNo 商户订单号
     * @param int $totalAmount 订单金额，单位：分
     * @param string $orderInfo 订单标题
     * @param array $extraData 额外参数
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=283
     */
    public function counterOrderSpecialCreate($outOrderNo, $totalAmount, $orderInfo, array $extraData = [])
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id']
        ], [
            'out_order_no' => $outOrderNo,
            'total_amount' => $totalAmount,
            'order_info' => $orderInfo,
            'order_efficient_time' => date('YmdHis', strtotime('+7 days')),
        ], $extraData);
        
        return $this->post('/api/v3/ccss/counter/order/special_create', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 收银台订单查询
     * @access public
     * @param string $outOrderNo 商户订单号
     * @param string $payOrderNo 拉卡拉订单号
     * @param string $channelId 渠道号
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=284
     */
    public function counterOrderQuery($outOrderNo, $payOrderNo = '', $channelId = '')
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id']
        ], [
            'out_order_no' => $outOrderNo,
            'pay_order_no' => $payOrderNo,
            'channel_id' => $channelId,
        ]);

        return $this->post('/api/v3/ccss/counter/order/query', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 收银台订单关单
     * @access public
     * @param string $outOrderNo 商户订单号
     * @param string $payOrderNo 拉卡拉订单号
     * @param string $channelId 渠道号
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=722
     */
    public function counterOrderClose($outOrderNo, $payOrderNo = '', $channelId = '')
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id']
        ], [
            'out_order_no' => $outOrderNo,
            'pay_order_no' => $payOrderNo,
            'channel_id' => $channelId,
        ]);

        return $this->post('/api/v3/ccss/counter/order/close', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }


    /**
     * 收银台退货退单
     * @access public
     * @param string $outTradeNo 商户请求流水号
     * @param string $refundAmount 退款金额
     * @param string $requestIp 客户端真实IP
     * @return array
     * @link https://o.lakala.com/#/home/document/detail?id=892
     */
    public function tradeOrderRefund($outTradeNo,$refundAmount,$requestIp)
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no'     => $this->options['term_no'],
        ], [
            'out_trade_no'      => $outTradeNo,
            'refund_amount'     => $refundAmount,
            'location_info'     => ['request_ip'=>$requestIp],
        ]);
        return $this->post('/api/v3/rfd/refund_front/refund', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData
        ]);
    }

    /**
     * 收银台退货查询
     * @access public
     * @param string $outTradeNo 商户请求流水号
     * @param string $tradeNo 拉卡拉交易流水号
     * @return array
     * @link https://o.lakala.com/#/home/document/detail?id=893
     */
    public function orderRefundQuery(string $outTradeNo,string $tradeNo = '')
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no'     => $this->options['term_no'],
        ], [
            'out_trade_no'      => $outTradeNo,
            'trade_no'          => $tradeNo,
        ]);
        return $this->post('/api/v3/rfd/refund_front/refund_query', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }
}
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

class AggregationScan extends Base
{
    /**
     * 接口版本
     * @var string
     */
    protected $apiVersion = '3.0';

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
        // 终端号
        'term_no' => '',
        // 证书私钥内容
        'private_key' => '',
        // 异步通知验签证书内容
        'certificate' => '',
        // 是否测试环境
        'test_env' => false,
    ];

    /**
     * 聚合主扫
     * @access public
     * @param string $outTradeNo 商户订单号
     * @param string $totalAmount 金额
     * @param string $accountType 钱包类型
     * @param string $transType 接入方式
     * @param string $ip 请求方IP地址
     * @param array $extraData 额外参数
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=110
     */
    public function transPreorder($outTradeNo, $totalAmount, $ip, $accountType = 'ALIPAY', $transType = '41', array $extraData = [])
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_trade_no' => $outTradeNo,
            'total_amount' => $totalAmount,
            'account_type' => $accountType,
            'trans_type' => $transType,
            'location_info' => [
                'request_ip' => $ip,
            ]
        ], $extraData);

        return $this->post('/api/v3/labs/trans/preorder', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 聚合被扫
     * @access public
     * @param string $outTradeNo 商户订单号
     * @param string $totalAmount 金额
     * @param string $accountType 钱包类型
     * @param string $transType 接入方式
     * @param string $ip 请求方IP地址
     * @param array $extraData 额外参数
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=112
     */
    public function transMicropay($outTradeNo, $totalAmount, $ip, $accountType = 'ALIPAY', $transType = '41', array $extraData = [])
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_trade_no' => $outTradeNo,
            'total_amount' => $totalAmount,
            'account_type' => $accountType,
            'trans_type' => $transType,
            'location_info' => [
                'request_ip' => $ip,
            ]
        ], $extraData);

        return $this->post('/api/v3/labs/trans/micropay', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 聚合扫码-交易查询
     * @access public
     * @param string $outTradeNo 商户交易流水号
     * @param string $tradeNo 拉卡拉交易流水号
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=116
     */
    public function queryTradequery($outTradeNo = '', $tradeNo = '')
    {
        // 请求体
        $reqData = [
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
        ];
        // 商户交易流水号不为空
        if (!empty($outTradeNo)) {
            $reqData['out_trade_no'] = $outTradeNo;
        }
        // 拉卡拉交易流水号不为空
        if (!empty($tradeNo)) {
            $reqData['trade_no'] = $tradeNo;
        }
        return $this->post('/api/v3/labs/query/tradequery', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 关单
     * @access public
     * @param string $originOutTradeNo 商户交易流水号
     * @param string $originTradeNo 拉卡拉交易流水号
     * @param string $requestIp 请求IP地址
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=115
     */
    public function relationClose($originOutTradeNo = '', $originTradeNo = '', $requestIp = '')
    {
        // 请求体
        $reqData = [
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'location_info' => [
                'request_ip' => $requestIp,
            ]
        ];
        // 商户交易流水号不为空
        if (!empty($originOutTradeNo)) {
            $reqData['origin_out_trade_no'] = $originOutTradeNo;
        }
        // 拉卡拉交易流水号不为空
        if (!empty($originTradeNo)) {
            $reqData['origin_trade_no'] = $originTradeNo;
        }
        return $this->post('/api/v3/labs/relation/close', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 扫码-撤销
     * @access public
     * @param string $outTradeNo 商户交易流水号
     * @param string $originOutTradeNo 商户交易流水号
     * @param string $originTradeNo 拉卡拉交易流水号
     * @param string $requestIp 请求IP地址
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=114
     */
    public function relationRevoked($outTradeNo = '', $originOutTradeNo = '', $originTradeNo = '', $requestIp = '')
    {
        // 请求体
        $reqData = [
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_trade_no' => $outTradeNo,
            'location_info' => [
                'request_ip' => $requestIp,
            ]
        ];
        // 商户交易流水号不为空
        if (!empty($originOutTradeNo)) {
            $reqData['origin_out_trade_no'] = $originOutTradeNo;
        }
        // 拉卡拉交易流水号不为空
        if (!empty($originTradeNo)) {
            $reqData['origin_trade_no'] = $originTradeNo;
        }
        return $this->post('/api/v3/labs/relation/revoked', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 扫码-退款交易
     * @access public
     * @param string $outTradeNo 商户订单号
     * @param string $refundAmount 退款金额
     * @param string $ip 请求方IP地址
     * @param array $extraData 额外参数
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=113
     */
    public function relationRefund($outTradeNo, $refundAmount, $ip, array $extraData = [])
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_trade_no' => $outTradeNo,
            'refund_amount' => $refundAmount,
            'location_info' => [
                'request_ip' => $ip,
            ]
        ], $extraData);

        return $this->post('/api/v3/labs/relation/refund', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 商户订单退款交易
     * @access public
     * @param string $outRefundOrderNo 商户退款订单号
     * @param string $refundAmount 退款金额
     * @param string $ip 请求方IP地址
     * @param array $extraData 额外参数
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=318
     */
    public function relationIdmrefund($outRefundOrderNo, $refundAmount, $ip, array $extraData = [])
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_refund_order_no' => $outRefundOrderNo,
            'refund_amount' => $refundAmount,
            'location_info' => [
                'request_ip' => $ip,
            ]
        ], $extraData);
        
        return $this->post('/api/v3/labs/relation/idmrefund', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 聚合扫码-交易查询
     * @access public
     * @param string $outRefundOrderNo 退款时的商户退款订单号
     * @return array
     * @link http://open.lakala.com/#/home/document/detail?id=116
     */
    public function queryIdmrefundquery($outRefundOrderNo = '')
    {
        // 请求体
        $reqData = [
            'merchant_no' => $this->options['merc_id'],
            'term_no' => $this->options['term_no'],
            'out_refund_order_no' => $outRefundOrderNo,
        ];
        return $this->post('/api/v3/labs/query/idmrefundquery', [
            'req_time' => date('YmdHis'),
            'version' => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }
}
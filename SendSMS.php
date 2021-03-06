<?php
/**
 * Describe
 * User          黄力军
 * DateAdded     2017/6/20
 * DateModified
 */

use AliyunMNS\Client;
use AliyunMNS\Topic;
use AliyunMNS\Constants;
use AliyunMNS\Model\MailAttributes;
use AliyunMNS\Model\SmsAttributes;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;
class SendSMS
{
	private $endPoint;
	private $accessId;
	private $accessKey;
	private $client;
	public function run($accessId, $accessKey, $phone, $params, $templateCode)
	{
		/**
		 * Step 1. 初始化Client
		 */
		$this->endPoint = "http://1292860968729749.mns.cn-hangzhou.aliyuncs.com/"; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
		$this->accessId = $accessId;
		$this->accessKey = $accessKey;
		$this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
		/**
		 * Step 2. 获取主题引用
		 */
		$topicName = "sms.topic-cn-hangzhou";
		$topic = $this->client->getTopicRef($topicName);
		/**
		 * Step 3. 生成SMS消息属性
		 */
		// 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
		$batchSmsAttributes = new BatchSmsAttributes("宁德众城企业管理", $templateCode);
		// 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
		$batchSmsAttributes->addReceiver($phone, $params);
		$messageAttributes = new MessageAttributes(array($batchSmsAttributes));
		/**
		 * Step 4. 设置SMS消息体（必须）
		 *
		 * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
		 */
		$messageBody = "smsmessage";
		/**
		 * Step 5. 发布SMS消息
		 */
		$request = new PublishMessageRequest($messageBody, $messageAttributes);
		try
		{
			$res = $topic->publishMessage($request);
			return $res->isSucceed();
		}
		catch (MnsException $e)
		{
			return false;
		}
	}
}
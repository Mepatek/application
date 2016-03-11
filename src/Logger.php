<?php

namespace Mepatek;

use Psr\Log\AbstractLogger,
	Nette\Database\Context,
	Nette\Security\User,
	Nette\Http\IRequest;

/**
 * Class Logger
 * @package Mepatek\Logger
 */
class Logger extends AbstractLogger
{

	/** @var Context */
	private $database;
	/** @var User */
	private $user;
	/** @var IRequest */
	private $httpRequest;

	public function __construct(Context $database, User $user, IRequest $httpRequest)
	{
		$this->database = $database;
		$this->user = $user;
		$this->httpRequest = $httpRequest;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return null
	 */
	public function log($level, $message, array $context = [])
	{
		$logdatetime = new \DateTime();
		$UzivatelID = $this->getAndRemoveContextValue($context, "UzivatelID", $this->user->id);
		$presenter = $this->getAndRemoveContextValue($context, "presenter");
		$function = $this->getAndRemoveContextValue($context, "function");
		$ip = $this->httpRequest->getRemoteAddress();
		$message = $this->interpolate($message, $context);

		$this->database->table("SYS_Log")->insert(
			[
				"UzivatelID"  => $UzivatelID,
				"ip"          => $ip,
				"presenter"   => $presenter,
				"function"    => $function,
				"level"       => $level,
				"logdatetime" => $logdatetime,
				"message"     => $message,
			]
		);
	}

	/**
	 * Get value from context array with key from list in keys
	 * Internal
	 *
	 * @param array  $context
	 * @param string $keys comma separated list of key
	 * @param mixed  $default default value
	 *
	 * @return mixed
	 */
	private function getAndRemoveContextValue(&$context, $keys, $default = null)
	{
		$akeys = explode(",", $keys);
		foreach ($akeys as $key) {
			if (isset($context[$key])) {
				$value = $context[$key];
				unset($context[$key]);
				return $value;
			}
		}
		return $default;
	}

	/**
	 * Interpolates context values into the message placeholders.
	 * Internal
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return string
	 */
	private function interpolate($message, array $context = [])
	{
		// build a replacement array with braces around the context keys
		$replace = [];
		foreach ($context as $key => $val) {
			$replace['{' . $key . '}'] = $val;
		}

		// interpolate replacement values into the message and return
		return strtr($message, $replace);
	}
}

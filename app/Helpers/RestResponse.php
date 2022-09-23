<?php

	/**
	 * Basic Class for making rest json response
	 * Class RestResponse
	 */
	class RestResponse
	{
		/**
		 * @var int $code
		 */
		public $code;

		/**
		 * @var string $status
		 */
		public $status;

		/**
		 * @var string $message
		 */
		public $message;

		/**
		 * @var array $payload
		 */
		public $payload;

		/**
		 * @var null|mixed $session
		 */
		//public $session;

		public function __construct($status , $message , $payload)
		{
			$this->status  = $status;

			switch ($status) {
				case 'success':
					$this->code = 200;
					if ($message == "") {
						$message = "success";
					}
					break;
                case 'created':
                    $this->code = 201;
                    if ($message == "") {
                        $message = "Created";
                    }
                    break;
                case 'nocontent':
                    $this->code = 204;
                    if ($message == "") {
                        $message = "No Content";
                    }
                    break;
				case 'redirect':
					$this->code = 302;
					if ($message == "") {
						$message = "Redirect";
					}
					break;
				case 'notmodified':
					$this->code = 304;
					break;
				case 'badrequest':
					$this->code = 400;
					if ($message == "") {
						$message = "Bad Request";
					}
					break;
				case 'unauthorized':
					$this->code = 401;
					if ($message == "") {
						$message = "Unauthorized";
					}
					break;
				case 'forbidden':
					$this->code = 403;
					if ($message == "") {
						$message = "Forbidden";
					}
					break;
				case 'notfound':
					$this->code = 404;
                    if ($message == "") {
                        $message = "NotFound";
                    }
					break;
				case 'error':
					$this->code = 500;
					break;
				default:
					throw new Exception('RestResponse Exception: Status not supported.');
					break;
			}
			$this->message = $message;
			$this->payload = $payload;
		}

		/**
		 * Convert response in json format
		 *
		 * @return \Laravel\Lumen\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
		 */
		public function toJSON()
		{
			$resp = response(json_encode($this),
			                 $this->code)
                ->header('Content-Type', 'application/json');
			return $resp;
		}

		/**
		 * Get Code
		 *
		 * @return int
		 */
		public function getCode()
		{
			return $this->code;
		}

		/**
		 * Set Code
		 *
		 * @param int $code
		 */
		public function setCode($code)
		{
			$this->code = $code;
		}

		/**
		 * Get Status
		 *
		 * @return string
		 */
		public function getStatus()
		{
			return $this->status;
		}

		/**
		 * Set Status
		 *
		 * @param string $status
		 */
		public function setStatus($status)
		{
			$this->status = $status;
		}

		/**
		 * Get Message
		 *
		 * @return string
		 */
		public function getMessage()
		{
			return $this->message;
		}

		/**
		 * Set Message
		 *
		 * @param string $message
		 */
		public function setMessage($message)
		{
			$this->message = $message;
		}

		/**
		 * Get Payload
		 *
		 * @return array
		 */
		public function getPayload()
		{
			return $this->payload;
		}

		/**
		 * Set Payload
		 *
		 * @param array $payload
		 */
		public function setPayload($payload)
		{
			$this->payload = $payload;
		}

		/**
		 * Get Session
		 *
		 * @return mixed|null
		 */
		public function getSession()
		{
			return $this->session;
		}

		/**
		 * Set Session
		 *
		 * @param mixed|null $session
		 */
		public function setSession($session)
		{
			$this->session = $session;
		}
	}

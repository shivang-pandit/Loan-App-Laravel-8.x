<?php

	/**
	 * Rest Response factory Class to make well formatted rest response
	 * Class RestResponseFactory
	 */
	class RestResponseFactory
	{

		/**
		 *
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function success($payload = "", $message = "")
		{
			return new RestResponse('success', $message, $payload);
		}

        /**
         *
         *
         * @param string $payload
         * @param string $message
         *
         * @return RestResponse
         */
        public static function created($payload = "", $message = "")
        {
            return new RestResponse('created', $message, $payload);
        }

        /**
         *
         *
         * @param string $payload
         * @param string $message
         *
         * @return RestResponse
         */
        public static function nocontent($payload = "", $message = "")
        {
            return new RestResponse('nocontent', $message, $payload);
        }


		/**
		 * Rest response with 302 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function redirect($payload = "", $message = "")
		{
			return new RestResponse('redirect', $message, $payload);
		}


		/**
		 * Rest response with 304 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function not_modified($payload = "", $message = "")
		{
			return new RestResponse('notmodified', $message, $payload);
		}


		/**
		 * Rest response with 400 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function badrequest($payload = "", $message = "")
		{
			return new RestResponse('badrequest', $message, $payload);
		}


		/**
		 * Rest response with 401 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function unauthorized($payload = "", $message = "")
		{
			return new RestResponse('unauthorized', $message, $payload);
		}


		/**
		 * Rest response with 403 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function forbidden($payload = "", $message = "")
		{
			return new RestResponse('forbidden', $message, $payload);
		}


		/**
		 * Rest response with 404 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function not_found($payload = "", $message = "")
		{
			return new RestResponse('notfound', $message, $payload);
		}


		/**
		 * Rest response with 500 status code.
		 *
		 * @param string $payload
		 * @param string $message
		 *
		 * @return RestResponse
		 */
		public static function error($payload = "", $message = "")
		{
			return new RestResponse('error', $message, $payload);
		}
	}

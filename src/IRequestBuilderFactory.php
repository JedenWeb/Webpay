<?php

namespace JedenWeb\Webpay;

/**
 * @author Pavel Jurásek
 */
interface IRequestBuilderFactory
{

	/**
	 * @return RequestBuilder
	 */
	public function create();

}

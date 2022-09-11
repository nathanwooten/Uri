<?php

namespace nathanwooten;

use Exception;

class Uri implements UriInterface
{

	public string $uri;

	protected $param = [];

	public function __construct( $uri = null, array $params = [] )
	{

		$this->uri = ! is_null( $uri ) ? $uri : $_SERVER[ 'REQUEST_URI' ];

		foreach ( $params as $paramName => $paramValue ) {
			$this->param[ $paramName ] = $paramValue;
		}

	}

	public function withUri( string $uri ) : UriInterface
	{

		$clone = clone $this;
		$clone->uri = $uri;

		return $clone;

	}

	public function getUri()
	{

		if ( is_null( $this->uri ) ) {
			return '/';
		}

		return $this->uri;

	}

	public function getTarget()
	{

		if ( ! isset( $this->target ) ) {

			$this->target = '';

			$path = $this->getComponent( PHP_URL_PATH );
			$query = $this->getComponent( PHP_URL_QUERY );
			$fragment = $this->getComponent( PHP_URL_FRAGMENT );

			if ( $path ) {
				$this->target .= $path;				
			}
			if ( $query ) {
				$this->target .= '?' . $query;
			}
			if ( $fragment ) {
				$this->target .= $fragment;
			}

		}

		return $this->target;

	}

	public function getComponent( $phpUrlConstant )
	{

		return parse_url( $this->getUri(), $phpUrlConstant );

	}

	public function withComponent( $phpUrlConstant, $value )
	{

		$components = [];
		$componentConstants = [ PHP_URL_SCHEME, PHP_URL_HOST, PHP_URL_PORT, PHP_URL_USER, PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY, PHP_URL_FRAGMENT ];

		foreach ( $componentConstants as $int ) {
			if ( $phpUrlConstant === $int ) {
				$components[ $phpUrlConstant ] = $value;

			} else {
				$components[ $int ] = $this->getComponent( $int );

			}
		}

		return $this->withUri( implode( '', $components ) );

	}

	public function getParam( $name )
	{

		return isset( $this->param[ $name ] ) ? $this->param[ $name ] : null;

	}

}

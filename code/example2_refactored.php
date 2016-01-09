<?php
class Currency {
  private static $usd_exchange_rate = 1.4;

  public static function in_usd($amount) {
    return $amount * self::$usd_exchange_rate;
  }
}

echo Currency::in_usd(100);
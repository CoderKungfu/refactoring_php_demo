<?php
class Currency {
  const USD_EXCHANGE_RATE = 1.4;

  public static function in_usd($amount) {
    return $amount * self::USD_EXCHANGE_RATE;
  }
}

echo Currency::in_usd(100);
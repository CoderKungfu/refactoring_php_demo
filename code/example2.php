<?php

class Currency {
  public static function in_usd($amount) {
    return $amount * 1.4;
  }
}

echo Currency::in_usd(100);
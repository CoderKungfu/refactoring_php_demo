# Refactoring Technique 2:

## No magic numbers / variables.

```php
class Currency {
  public static function in_usd($amount) {
    return $amount * 1.4;
  }
}

echo Currency::in_usd(100);
```

What is `1.4`? Bring out the meaning with a variable or a constant.

```php
class Currency {
  private static $usd_exchange_rate = 1.4;

  public static function in_usd($amount) {
    return $amount * self::$usd_exchange_rate;
  }
}

echo Currency::in_usd(100);
```
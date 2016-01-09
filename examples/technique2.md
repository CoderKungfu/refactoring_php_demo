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

**The refactored code:**

```php
class Currency {
  const USD_EXCHANGE_RATE = 1.4;

  public static function in_usd($amount) {
    return $amount * self::USD_EXCHANGE_RATE;
  }
}

echo Currency::in_usd(100);
```
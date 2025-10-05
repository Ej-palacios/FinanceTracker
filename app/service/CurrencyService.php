<?php

namespace App\Service;

class CurrencyService
{
    // Tasas de cambio actualizadas (1 NIO = X unidades de otras monedas)
    private const EXCHANGE_RATES = [
        'NIO' => 1,           // Córdoba Nicaragüense (base)
        'USD' => 0.028,       // 1 NIO = 0.028 USD (aprox 35.71 NIO = 1 USD)
        'EUR' => 0.026,       // 1 NIO = 0.026 EUR (aprox 38.46 NIO = 1 EUR)
    ];

    // Símbolos de moneda
    private const CURRENCY_SYMBOLS = [
        'NIO' => 'C$',
        'USD' => '$',
        'EUR' => '€',
    ];

    /**
     * Convierte un monto de una moneda a otra
     *
     * @param float $amount Monto a convertir
     * @param string $fromCurrency Moneda origen
     * @param string $toCurrency Moneda destino
     * @return float Monto convertido
     */
    public static function convert(float $amount, string $fromCurrency = 'NIO', string $toCurrency = 'NIO'): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Convertir a NIO primero (moneda base)
        $amountInNIO = $amount / self::EXCHANGE_RATES[$fromCurrency];

        // Convertir de NIO a moneda destino
        return $amountInNIO * self::EXCHANGE_RATES[$toCurrency];
    }

    /**
     * Obtiene el símbolo de una moneda
     *
     * @param string $currency Código de moneda
     * @return string Símbolo de la moneda
     */
    public static function getSymbol(string $currency): string
    {
        return self::CURRENCY_SYMBOLS[$currency] ?? $currency;
    }

    /**
     * Obtiene la tasa de cambio entre dos monedas
     *
     * @param string $fromCurrency Moneda origen
     * @param string $toCurrency Moneda destino
     * @return float Tasa de cambio
     */
    public static function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $fromRate = self::EXCHANGE_RATES[$fromCurrency] ?? 1;
        $toRate = self::EXCHANGE_RATES[$toCurrency] ?? 1;

        return $toRate / $fromRate;
    }

    /**
     * Formatea un monto con el símbolo de moneda
     *
     * @param float $amount Monto
     * @param string $currency Código de moneda
     * @param int $decimals Número de decimales
     * @return string Monto formateado
     */
    public static function format(float $amount, string $currency, int $decimals = 2): string
    {
        $symbol = self::getSymbol($currency);
        return $symbol . ' ' . number_format($amount, $decimals, '.', ',');
    }

    /**
     * Convierte y formatea un monto desde NIO a la moneda del usuario
     *
     * @param float $amount Monto en NIO
     * @param string $userCurrency Moneda del usuario
     * @return array ['amount' => float, 'formatted' => string]
     */
    public static function convertAndFormat(float $amount, string $userCurrency): array
    {
        $convertedAmount = self::convert($amount, 'NIO', $userCurrency);
        $formatted = self::format($convertedAmount, $userCurrency);

        return [
            'amount' => $convertedAmount,
            'formatted' => $formatted,
        ];
    }
}

<?php

use Illuminate\Support\Carbon;

if (!function_exists('normalize_phone')) {
    /**
     * Приводит номер телефона к формату 9XXXXXXXXX, исключая +7, 8, пробелы, скобки и т.д.
     * Номера, начинающиеся на 811 или 812, сохраняются как есть.
     */
    function normalize_phone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (preg_match('/^81[12]/', $digits)) {
            return $digits;
        }

        if (str_starts_with($digits, '8') || str_starts_with($digits, '7')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }
}

if (! function_exists('camel_to_snake')) {
    /**
     * Преобразует строку из CamelCase / PascalCase в snake_case.
     *
     * Примеры:
     *   Success      → success
     *   NotAvailable → not_available
     *   NotFound     → not_found
     *
     * @param string|null $value
     * @return string|null
     */
    function camel_to_snake(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $value));
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Универсальный метод для форматирования даты/времени с поддержкой временных зон.
     *
     * @param mixed $datetime Входные данные даты/времени (строка, объект DateTime или Carbon)
     * @param string $outputFormat Желаемый формат вывода (например, 'Y-m-d H:i:s')
     * @param string|null $inputFormat Формат входных данных, если известен (например, 'Ymd\THis\Z')
     * @param string|null $fromTimezone Исходная временная зона данных (например, 'UTC', 'Europe/Moscow')
     * @param string|null $toTimezone Целевая временная зона для преобразования (например, 'Europe/Moscow')
     * @return string|Carbon|null Отформатированная строка или объект Carbon (если $outputFormat = null)
     *
     * @example
     * // Преобразование времени АТС в московское время
     * format_datetime('20231220T123000Z', 'Y-m-d H:i:s', 'Ymd\THis\Z', 'UTC', 'Europe/Moscow')
     * // Вернет: "2023-12-20 15:30:00" (перевод из UTC в Москву)
     *
     * @example
     * // Преобразование московского времени в UTC
     * format_datetime('2023-12-20 15:30:00', 'Y-m-d H:i:s', null, 'Europe/Moscow', 'UTC')
     * // Вернет: "2023-12-20 12:30:00" (перевод из Москвы в UTC)
     *
     * @example
     * // Простое форматирование без смены временной зоны
     * format_datetime('2023-12-20 12:30:00', 'd.m.Y H:i')
     * // Вернет: "20.12.2023 12:30"
     */
    function format_datetime($datetime, string $outputFormat = 'Y-m-d H:i:s', ?string $inputFormat = null, ?string $fromTimezone = null, ?string $toTimezone = null)
    {
        if (empty($datetime)) {
            return null;
        }

        // Парсим входные данные
        if ($inputFormat) {
            // Если указан формат входных данных, используем createFromFormat
            $carbon = $fromTimezone
                ? Carbon::createFromFormat($inputFormat, $datetime, $fromTimezone)
                : Carbon::createFromFormat($inputFormat, $datetime);
        } else {
            // Если формат не указан, используем автоматическое определение
            $carbon = $fromTimezone
                ? Carbon::parse($datetime, $fromTimezone)
                : Carbon::parse($datetime);
        }

        // Применяем преобразование временной зоны, если указано
        if ($toTimezone) {
            $carbon = $carbon->setTimezone($toTimezone);
        }

        return $outputFormat ? $carbon->format($outputFormat) : $carbon;
    }
}

if (!function_exists('utc_to_moscow')) {
    /**
     * Конвертирует время из UTC в московский часовой пояс.
     *
     * @param mixed $datetime Время в UTC (строка, DateTime или Carbon)
     * @param string|null $format Формат вывода (если null, вернет объект Carbon)
     * @return string|Carbon|null
     *
     * @example
     * utc_to_moscow('2023-12-20 12:30:00') // "2023-12-20 15:30:00"
     * utc_to_moscow('20231220T123000Z', 'Y-m-d H:i:s') // "2023-12-20 15:30:00"
     */
    function utc_to_moscow($datetime, ?string $format = 'Y-m-d H:i:s')
    {
        return format_datetime($datetime, $format, null, 'UTC', 'Europe/Moscow');
    }
}

if (!function_exists('moscow_to_utc')) {
    /**
     * Конвертирует время из московского часового пояса в UTC.
     *
     * @param mixed $datetime Время в московском поясе (строка, DateTime или Carbon)
     * @param string|null $format Формат вывода (если null, вернет объект Carbon)
     * @return string|Carbon|null
     *
     * @example
     * moscow_to_utc('2023-12-20 15:30:00') // "2023-12-20 12:30:00"
     * moscow_to_utc('20.12.2023 15:30', 'Y-m-d H:i:s') // "2023-12-20 12:30:00"
     */
    function moscow_to_utc($datetime, ?string $format = 'Y-m-d H:i:s')
    {
        return format_datetime($datetime, $format, null, 'Europe/Moscow', 'UTC');
    }
}


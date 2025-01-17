<?php

namespace Classes;

class Helpers
{
    // Güvenli HTML çıktı fonksiyonu
    public static function safeHtml($string)
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }

    // Hata mesajlarını kullanıcıya göster
    public static function showErrorMessage($message)
    {
        echo "<p style='color: red;'>Hata: " . self::safeHtml($message) . "</p>";
    }

    // Başarı mesajlarını kullanıcıya göster
    public static function showSuccessMessage($message)
    {
        echo "<p style='color: green;'>Başarılı: " . self::safeHtml($message) . "</p>";
    }
}

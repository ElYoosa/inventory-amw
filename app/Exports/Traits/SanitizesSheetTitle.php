<?php

namespace App\Exports\Traits;

trait SanitizesSheetTitle
{
  protected function sanitizeSheetTitle(string $title): string
  {
    $title = preg_replace("/[:\\\\\/\*\?\[\]]+/", "", $title);
    return mb_substr($title, 0, 31);
  }
}

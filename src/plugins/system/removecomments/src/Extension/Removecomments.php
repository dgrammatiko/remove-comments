<?php

/**
 * @copyright  (C) 2024 Dimitrios Grammatikogiannis
 * @license    GNU General Public License version 3 or later
 */

namespace Dgrammatiko\Plugin\System\Removecomments\Extension;

\defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;

final class Removecomments extends CMSPlugin
{
  private static $files = [
    '/libraries/vendor/voku/portable-ascii/src/voku/helper/ASCII.php',
    'www/libraries/vendor/voku/portable-utf8/src/voku/helper/UTF8.php',
  ];

  public static function getSubscribedEvents(): array {
    return ['onUpdateAfterCompete' => 'doit'];
  }

  public function doit() {
    foreach (self::$files as $file) $this->removeComments(JPATH_ROOT . $file);
  }

  protected function removeComments($file): void {
    if (!is_file($file)) return;

    $content       = file_get_contents($file);
    $newStr        = '';
    $commentTokens = [T_COMMENT];

    if (defined('T_DOC_COMMENT')) {
      $commentTokens[] = T_DOC_COMMENT;
    }

    if (defined('T_ML_COMMENT')) {
      $commentTokens[] = T_ML_COMMENT;
    }

    $tokens = token_get_all($content);

    foreach ($tokens as $token) {
      if (is_array($token)) {
        if (in_array($token[0], $commentTokens)) continue;

        $token = $token[1];
      }

      $newStr .= $token;
    }

    file_put_contents($file, $newStr);
  }
}

<?php

namespace App\Logging;

/**
 * Verantwortlich den Log-Pfad Dynamisch zu setzten
 */
class DynamicLogPath
{
    public function __invoke(mixed $logger): void
    {
        if (function_exists('posix_getpwuid')) {
            $user_id = posix_geteuid();
            $user = posix_getpwuid($user_id);
            if (!empty($user)) {
                $user = $user['name'];
            } else {
                $user = strtolower(str_replace(' ', '', get_current_user()));
            }
        } else {
            $user = strtolower(str_replace(' ', '', get_current_user()));
        }

        foreach ($logger->getHandlers() as $handler) {
            if (!method_exists($handler, 'setFilenameFormat')) {
                continue;
            }
            /** @phpstan-ignore-next-line */
            $handler->setFilenameFormat("{filename}-{$user}-{date}", 'Y-m-d');
        }
    }
}

<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class PhpMailTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $recipients = $message->getEnvelope()->getRecipients();

        if (empty($recipients)) {
            throw new TransportException('Нет получателей письма.');
        }

        $to = implode(', ', array_map(
            static fn ($address) => $address->toString(),
            $recipients
        ));

        $rawMessage = $message->toString();
        $parts = preg_split("/\r\n\r\n|\n\n|\r\r/", $rawMessage, 2);

        $rawHeaders = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        $subject = '';
        $filteredHeaders = [];

        $headerLines = preg_split("/\r\n|\n|\r/", $rawHeaders) ?: [];

        foreach ($headerLines as $line) {
            if (preg_match('/^Subject:\s*(.*)$/i', $line, $matches)) {
                $subject = trim($matches[1]);
                continue;
            }

            if (preg_match('/^To:\s*/i', $line)) {
                continue;
            }

            $filteredHeaders[] = $line;
        }

        $headers = implode("\r\n", $filteredHeaders);

        $sender = $message->getEnvelope()->getSender();
        $params = $sender
            ? '-f' . escapeshellarg($sender->getAddress())
            : '';

        $sent = $params !== ''
            ? mail($to, $subject, $body, $headers, $params)
            : mail($to, $subject, $body, $headers);

        if (! $sent) {
            throw new TransportException('PHP mail() вернул false.');
        }
    }

    public function __toString(): string
    {
        return 'php-mail://default';
    }
}
<?php

namespace App\Services;

use App\DTOs\DockerExecResult;
use GuzzleHttp\Client;
use RuntimeException;

class DockerService
{
    private const API_VERSION = 'v1.44';

    public function __construct(
        private readonly string $socketPath = '/var/run/docker.sock',
    ) {}

    /**
     * Execute a command inside a running container and return the result.
     *
     * Uses the Docker Engine API over the Unix socket. The exec runs with
     * TTY disabled so stdout and stderr are delivered as a multiplexed stream;
     * both are captured and concatenated in the returned output string.
     *
     * @param  string[]  $command
     *
     * @throws RuntimeException if the container is not found or the socket is unavailable
     */
    public function exec(string $container, array $command): DockerExecResult
    {
        $client = $this->client();

        $execId = $this->createExec($client, $container, $command);
        $output = $this->startExec($client, $execId);
        $exitCode = $this->inspectExec($client, $execId);

        return new DockerExecResult($exitCode, $output);
    }

    private function createExec(Client $client, string $container, array $command): string
    {
        $response = $client->post(
            $this->url("containers/{$container}/exec"),
            [
                'json' => [
                    'AttachStdout' => true,
                    'AttachStderr' => true,
                    'Tty' => false,
                    'Cmd' => $command,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true)['Id'];
    }

    /**
     * Start the exec instance and decode the multiplexed stream Docker returns
     * when Tty is false. Each frame is prefixed with an 8-byte header:
     *   byte 0:   stream type (0=stdin, 1=stdout, 2=stderr)
     *   bytes 1-3: padding
     *   bytes 4-7: frame size (big-endian uint32)
     */
    private function startExec(Client $client, string $execId): string
    {
        $response = $client->post(
            $this->url("exec/{$execId}/start"),
            ['json' => ['Detach' => false, 'Tty' => false]]
        );

        return $this->demultiplex((string) $response->getBody());
    }

    private function inspectExec(Client $client, string $execId): int
    {
        $response = $client->get($this->url("exec/{$execId}/json"));

        return (int) json_decode((string) $response->getBody(), true)['ExitCode'];
    }

    private function demultiplex(string $raw): string
    {
        $output = '';
        $offset = 0;
        $length = strlen($raw);

        while ($offset + 8 <= $length) {
            $header = substr($raw, $offset, 8);
            /** @var array{1: int} $unpacked */
            $unpacked = unpack('N', substr($header, 4, 4));
            $frameSize = $unpacked[1];
            $offset += 8;

            if ($offset + $frameSize > $length) {
                break;
            }

            $output .= substr($raw, $offset, $frameSize);
            $offset += $frameSize;
        }

        return $output;
    }

    private function url(string $path): string
    {
        return 'http://localhost/'.self::API_VERSION.'/'.$path;
    }

    private function client(): Client
    {
        return new Client([
            'curl' => [CURLOPT_UNIX_SOCKET_PATH => $this->socketPath],
        ]);
    }
}

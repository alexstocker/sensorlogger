#!/usr/bin/env php
<?php

// phpcs:disable PSR1.Files.SideEffects -- This CLI entry point defines helpers and dispatches commands.

const DEFAULT_BASE_URL = 'http://localhost:8080';
const DEFAULT_BASIC_DEVICE_ID = 'sensorlogger-basic-demo';

function loadDevelopmentEnvironment(): void
{
    $directories = [getcwd(), __DIR__];
    foreach ($directories as $directory) {
        while ($directory !== '' && $directory !== DIRECTORY_SEPARATOR) {
            $environmentFile = $directory . DIRECTORY_SEPARATOR . '.env';
            if (is_file($environmentFile)) {
                loadSensorLoggerEnvironmentFile($environmentFile);
                return;
            }
            $parent = dirname($directory);
            if ($parent === $directory) {
                break;
            }
            $directory = $parent;
        }
    }
}

function loadSensorLoggerEnvironmentFile(string $environmentFile): void
{
    $lines = file($environmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        fail('Could not read development environment file: ' . $environmentFile);
    }

    foreach ($lines as $line) {
        if (preg_match('/^\s*(SENSORLOGGER_[A-Z0-9_]+)\s*=\s*(.*?)\s*$/', $line, $matches) !== 1) {
            continue;
        }

        $name = $matches[1];
        if (getenv($name) !== false) {
            continue;
        }

        $value = $matches[2];
        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

function usage(): void
{
    echo <<<USAGE
Usage: php scripts/sensorlogger-demo.php <command>

Commands:
  register          Register a demo device and its data types
  types             List the registered device's data types
  basic             Send one simulated temperature/humidity log
  extended          Send one simulated log for all registered data types
  help              Show this help

Configuration is read from environment variables:
  SENSORLOGGER_URL             ownCloud base URL (from .env or default)
  SENSORLOGGER_USERNAME        ownCloud username (from .env)
  SENSORLOGGER_APP_PASSWORD    ownCloud app password/passcode (from .env)
  SENSORLOGGER_DEVICE_ID       Device UUID for types and extended commands

The script loads SENSORLOGGER_* settings from the development .env file. Shell
environment variables take precedence.

After registration, set SENSORLOGGER_DEVICE_ID to the returned device ID before
using types or extended. The basic command uses a stable demo ID by default.

USAGE;
}

function fail(string $message): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function getConfiguration(): array
{
    if (!extension_loaded('curl')) {
        fail('The PHP cURL extension is required.');
    }

    $username = getenv('SENSORLOGGER_USERNAME');
    $password = getenv('SENSORLOGGER_APP_PASSWORD');
    if ($username === false || $username === '' || $password === false || $password === '') {
        fail('Set SENSORLOGGER_USERNAME and SENSORLOGGER_APP_PASSWORD before running this script.');
    }

    $baseUrl = getenv('SENSORLOGGER_URL');
    if ($baseUrl === false || $baseUrl === '') {
        $baseUrl = DEFAULT_BASE_URL;
    }

    return [
        'apiUrl' => rtrim($baseUrl, '/') . '/index.php/apps/sensorlogger/api/v1',
        'username' => $username,
        'password' => $password,
    ];
}

function request(array $configuration, string $endpoint, array $payload): array
{
    $url = $configuration['apiUrl'] . '/' . $endpoint . '/';
    $json = json_encode($payload, JSON_THROW_ON_ERROR);
    $handle = curl_init($url);
    if ($handle === false) {
        fail('Could not initialize an HTTP request.');
    }

    curl_setopt_array($handle, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
        ],
        CURLOPT_USERPWD => $configuration['username'] . ':' . $configuration['password'],
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($handle);
    $curlError = curl_error($handle);
    $status = (int)curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
    curl_close($handle);

    if ($response === false) {
        fail('HTTP request failed: ' . $curlError);
    }

    try {
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        fail('Server returned invalid JSON (HTTP ' . $status . ').');
    }

    if ($status < 200 || $status >= 300) {
        fail('Server returned HTTP ' . $status . ': ' . json_encode($data));
    }
    if (!is_array($data) || !($data['success'] ?? false)) {
        fail('SensorLogger API rejected the request: ' . json_encode($data));
    }

    return $data;
}

function getDeviceId(): string
{
    $deviceId = getenv('SENSORLOGGER_DEVICE_ID');
    if ($deviceId === false || $deviceId === '') {
        fail('Set SENSORLOGGER_DEVICE_ID to the device ID returned by the register command.');
    }

    return $deviceId;
}

function randomUuid(): string
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    $hex = bin2hex($bytes);

    return sprintf(
        '%s-%s-%s-%s-%s',
        substr($hex, 0, 8),
        substr($hex, 8, 4),
        substr($hex, 12, 4),
        substr($hex, 16, 4),
        substr($hex, 20)
    );
}

function printResponse(array $data): void
{
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
}

loadDevelopmentEnvironment();

$command = $argv[1] ?? 'help';
if ($command === 'help' || $command === '--help' || $command === '-h') {
    usage();
    exit(0);
}

if (!in_array($command, ['register', 'types', 'basic', 'extended'], true)) {
    usage();
    exit(2);
}

$configuration = getConfiguration();
$deviceId = getenv('SENSORLOGGER_DEVICE_ID');

switch ($command) {
    case 'register':
        $deviceId = ($deviceId !== false && $deviceId !== '') ? $deviceId : randomUuid();
        $response = request($configuration, 'registerdevice', [
            'deviceId' => $deviceId,
            'deviceName' => 'SensorLogger demo sensor',
            'deviceType' => 'Demo sensor',
            'deviceGroup' => 'Demo',
            'deviceParentGroup' => 'SensorLogger demo',
            'deviceDataTypes' => [
                [
                    'type' => 'temperature',
                    'description' => 'Temperature',
                    'unit' => '°C',
                ],
                [
                    'type' => 'humidity',
                    'description' => 'Humidity',
                    'unit' => '%',
                ],
                [
                    'type' => 'CO2',
                    'description' => 'Carbon dioxide',
                    'unit' => 'ppm',
                ],
            ],
        ]);
        printResponse($response);
        echo 'Device ID: ' . $deviceId . PHP_EOL;
        break;

    case 'types':
        printResponse(request($configuration, 'getdevicedatatypes', [
            'deviceId' => getDeviceId(),
        ]));
        break;

    case 'basic':
        $deviceId = ($deviceId !== false && $deviceId !== '')
            ? $deviceId
            : DEFAULT_BASIC_DEVICE_ID;
        printResponse(request($configuration, 'createlog', [
            'deviceId' => $deviceId,
            'date' => date('Y-m-d H:i:s'),
            'temperature' => random_int(-90, 499) / 10,
            'humidity' => random_int(10, 999) / 10,
        ]));
        break;

    case 'extended':
        $deviceId = getDeviceId();
        $typesResponse = request($configuration, 'getdevicedatatypes', [
            'deviceId' => $deviceId,
        ]);
        $types = $typesResponse['data'] ?? [];
        if (!is_array($types) || count($types) === 0) {
            fail('The device has no registered data types.');
        }

        $sensorData = [];
        foreach ($types as $type) {
            if (!isset($type['id'])) {
                fail('A data type response is missing its ID.');
            }
            $sensorData[] = [
                'dataTypeId' => (int)$type['id'],
                'value' => random_int(10, 1000) / 10,
            ];
        }

        printResponse(request($configuration, 'createlog', [
            'deviceId' => $deviceId,
            'date' => date('Y-m-d H:i:s'),
            'data' => $sensorData,
        ]));
        break;
}

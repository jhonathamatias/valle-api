<?php

namespace Valle\Services;

use Aws\S3\S3Client;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\UploadedFileInterface;

class File
{
    protected $fileSystem;
    protected $client;
    protected $bucket;

    public function __construct()
    {
        $this->client = new S3Client([
            'region' => getenv('AWS_S3_REGION'),
            'version' => getenv('AWS_S3_VERSION'),
            'credentials' => [
                'key' => getenv('AWS_S3_KEY'),
                'secret' => getenv('AWS_S3_SECRET')
            ]
        ]);

        $this->bucket = getenv('AWS_S3_BUCKET');
    }

    public function upload(UploadedFileInterface $file, string $path): string
    {
        try {
            $pathName = $path . '/'. $this->createFileName($file);

            $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $pathName,
                'Body'   => $file->getStream(),
            ]);
    
            return $pathName;
        } catch (\Aws\S3\Exception\S3Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getImage(string $path): Stream
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key'    => $path,
            ]);

            return $result->get('Body');
        } catch (\Aws\S3\Exception\S3Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getImageUrl(string $path): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $path
        ]);

        $request = $this->client->createPresignedRequest($cmd, '+20 minutes');

        return $request->getUri();
    }

    protected function createFileName($file)
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));

        return sprintf('%s.%0.8s', $basename, $extension);
    }
}

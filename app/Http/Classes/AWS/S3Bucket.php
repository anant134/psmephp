<?php
namespace App\Http\Classes\Aws;

use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class S3Bucket
{
    public function __construct()
    {

    }

    public static function UploadToBucket($key, $secret, $region, $bucket, $fileName, $pathToFile, $fileType)
    {
        try {
            $credentials = new Credentials($key, $secret);
            $s3Client = new S3Client([
                'region' => $region,
                'version' => '2006-03-01',
                'credentials' => $credentials,
            ]);
            $result = $s3Client->putObject(array(
                'Bucket' => $bucket,
                'Key' => $fileName,
                'Body' => fopen($pathToFile, 'r+'),
                //'ACL' => 'public-read',
                'ContentType' => $fileType,
            ));
            return true;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
            return false;
        }
    }

    public static function GetFromBucket($key, $secret, $region, $bucket, $filePath, $fileName, $isLimitedTimeAccess,$time_in_minutes=5)
    {
        $presignedUrl = null;
        try {
            $credentials = new Credentials($key, $secret);
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'version' => '2006-03-01',
                'credentials' => $credentials,
            ]);

            if($isLimitedTimeAccess){

                $cmd = $s3Client->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key'    => $filePath
                ]);
                $time_expire_minute = "+ ".$time_in_minutes." minutes";
                $request = $s3Client->createPresignedRequest($cmd, $time_expire_minute);
                $presignedUrl = (string) $request->getUri();

            }else{
                $presignedUrl = $s3Client->getObjectUrl($bucket, $filePath);
            }
            
            return $presignedUrl;

        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
            return false;
        }
    }

    public static function GetFileFromBucket($key, $secret, $region, $bucket, $filePath)
    {
        try {
            $credentials = new Credentials($key, $secret);
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'version' => '2006-03-01',
                'credentials' => $credentials,
            ]);

            $stream = $s3Client->getObject([
                'Bucket' => $bucket,
                'Key'    => $filePath
            ]);

            return $stream;

        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
        return false;
    }

    public static function CreateBucket($key, $secret, $region, $bucket)
    {
        try {
            $credentials = new Credentials($key, $secret);
            $s3Client = new S3Client([
                'region' => $region,
                'version' => '2006-03-01',
                'credentials' => $credentials,
            ]);
            $bucketCreation = $s3Client->createBucket([
                'Bucket' => $bucket
            ]);

            $bucketCORS = $s3Client->putBucketCors([
                'Bucket' => $bucket, // REQUIRED
                'CORSConfiguration' => [ // REQUIRED
                    'CORSRules' => [ // REQUIRED
                        [
                            'AllowedMethods' => ['GET'], // REQUIRED 'POST', 'GET', 'PUT'
                            'AllowedOrigins' => ['*'] // REQUIRED
                        ],
                    ],
                ]
            ]);
            
            return ($bucketCreation && $bucketCORS) ? true : false;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
            return false;
        }
    }

}

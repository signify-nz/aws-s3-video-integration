# AWS S3 Video Integration

CMS management for AWS S3 videos and buckets. Features to assist with integration of S3 videos.

## Requirements
* [SilverStripe CMS ^4](https://github.com/silverstripe/silverstripe-cms)

## Install

Install via [composer](https://getcomposer.org):

Add the following to the composer.json file:
```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:signify-nz/aws-s3-video-integration.git"
    }
],
```
Run the following command:
```bash
composer require signify-nz/aws-s3-video-integration
```

## Video Admin

CMS menu 'Videos' option to create and manage AWS S3 Videos and Buckets.

If only one S3 bucket has been created, the AWS video hides the bucket selection field
and will automatically select that bucket when saving/publishing.

Permissions to view, edit, create, and delete both buckets and videos are extendable,
but default to using the "Access to 'AWS Videos' section" permission.

## S3DropdownField

Can be used as part of a custom implementation for AWS videos.

Creates a CMS dropdown field with:
* AWS S3 videos as the source 
* Field description contains a link to the 'Videos' CMS menu option.

Example:

```php
$s3dropdown = S3DropdownField::create('S3VideoID', 'AWS video');
```

## (Optional) wysiwyg content editor integration

AWS videos can optionally be added via the wysiwg content editor.

Enable by applying the following extensions to the site configuration:

```yml
SilverStripe\AssetAdmin\Forms\RemoteFileFormFactory:
  extensions:
    - Signify\Extensions\RemoteFileFormExtension

SilverStripe\Admin\LeftAndMain:
  extensions:
    - Signify\Extensions\AwsVideoLeftAndMainExtension
```
* Use the wysiwyg editor's "Insert media via URL" option. 
* Below the "Embed URL" input will be a dropdown menu to select an AWS video. 
* Selecting a video will fill the URL input with the link for the AWS video.


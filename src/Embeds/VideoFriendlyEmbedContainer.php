<?php

namespace Signify\Embeds;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\View\Embed\EmbedContainer;

/**
 * Embed container that better supports direct video files (mp4, etc).
 */
class VideoFriendlyEmbedContainer extends EmbedContainer
{
    use Configurable;

    /**
     * Default width for direct video embeds.
     *
     * @config
     * @var int
     */
    private static int $direct_video_width = 640;

    /**
     * Aspect ratio height (numerator) for video.
     *
     * @config
     * @var int
     */
    private static int $aspect_ratio_height = 9;

    /**
     * Aspect ratio width (denominator) for video.
     *
     * @config
     * @var int
     */
    private static int $aspect_ratio_width = 16;

    /**
     * Video file extensions that will support the 'friendly' video embed behaviour.
     *
     * @config
     * @var array
     */
    private static array $direct_video_extensions  = ['mp4','webm','ogg'];

    /**
     * @var string
     */
    protected string $embedUrl;

    public function __construct(string $url)
    {
        // $url property in parent is private in the parent class so we cannot use it here.
        // So copy the value as 'embedUrl'
        $this->embedUrl = $url;
        parent::__construct($url);
    }

    /**
     * Use a more suitable height for direct video files.
     */
    public function getHeight(): int
    {
        if ($this->isDirectVideo()) {
            $width = $this->getWidth();
            $heightRatio = (int) self::config()->get('aspect_ratio_height');
            $widthRatio = (int) self::config()->get('aspect_ratio_width');

            if ($heightRatio <= 0 || $widthRatio <= 0) {
                throw new \InvalidArgumentException(sprintf(
                'Invalid aspect ratio config: aspect_ratio_height=%d, ' .
                'aspect_ratio_width=%d. Both must be positive integers.',
                    $heightRatio,
                    $widthRatio
                ));
            }
            return (int)($width * $heightRatio / $widthRatio);
        }

        return parent::getHeight();
    }

    /**
     * Use a more suitable width for direct video files.
     */
    public function getWidth(): int
    {
        if ($this->isDirectVideo()) {
            return self::config()->get('direct_video_width');
        }

        return parent::getWidth();
    }

    /**
     * Show nicer video placeholder image for preview/placeholder within TinyMCE.
     */
    public function getPreviewURL(): string
    {
        if ($this->isDirectVideo()) {
            return ModuleResourceLoader::resourceURL(
                'signify-nz/aws-s3-video-integration:images/icon_video.png'
            );
        }

        return parent::getPreviewURL();
    }

    /**
     * Get embed name.
     */
    public function getName(): string
    {
        if ($this->isDirectVideo()) {
            return basename($this->embedUrl);
        }
        return parent::getName();
    }

    /**
     * Get embed type.
     */
    public function getType(): string
    {
        if ($this->isDirectVideo()) {
            return 'video';
        }

        return parent::getType();
    }

    /**
     * Validate embed.
     */
    public function validate(): bool
    {
        // If direct video, check that it exists.
        if ($this->isDirectVideo()) {
            $client = new Client([
                'timeout' => 10,  // seconds
                'allow_redirects' => true,
                'http_errors' => false,  // Don’t throw exceptions on 4xx/5xx status
            ]);

            try {
                $response = $client->head($this->embedUrl);
                // Return true only if status code is 200 OK
                return $response->getStatusCode() === 200;
            } catch (GuzzleException $e) {
                return false;
            }
        }
        return parent::validate();
    }

    /**
     * Check whether the embed is a direct video.
     */
    public function isDirectVideo(): bool
    {
        $extensions = self::config()->get('direct_video_extensions');
        $pattern = '/\.(' . implode('|', $extensions) . ')$/i';

        return preg_match($pattern, $this->embedUrl) === 1;
    }
}

<?php

namespace Signify\Views\Shortcodes;

use Psr\SimpleCache\CacheInterface;
use Signify\Models\S3Bucket;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Flushable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\Embed\Embeddable;
use SilverStripe\View\Shortcodes\EmbedShortcodeProvider;

/**
 * Extends the default embed shortcode provider to avoid network requests to direct video files.
 *
 * Without this, memory issues are hit when embedding videos of a reasonable size,
 * due to calls to $embeddable->getExtractor();
 */
class VideoFriendlyEmbedShortcodeProvider extends EmbedShortcodeProvider implements Flushable
{
    use Configurable;

    /**
     * Cache time-to-live (TTL) in seconds for video domain caching.
     *
     * This controls how long the list of video domains (e.g. S3 buckets)
     * is cached before being refreshed.
     *
     * @config
     * @var int
     */
    private static int $domains_ttl = 300;

    /**
     * Override shortcode handler
     *
     * {@inheritdoc}
     */
    public static function handle_shortcode($arguments, $content, $parser, $shortcode, $extra = []): string
    {
        // Get service URL
        if (!empty($content)) {
            $serviceURL = $content;
        } elseif (!empty($arguments['url'])) {
            $serviceURL = $arguments['url'];
        } else {
            return '';
        }

        $embeddable = Injector::inst()->create(Embeddable::class, $serviceURL);

        if (method_exists($embeddable, 'isDirectVideo') && $embeddable->isDirectVideo()) {
            self::setExtractorUrl($serviceURL);

            self::updateDomainsExcludedFromSandboxing();
            $safeUrl = htmlspecialchars($serviceURL, ENT_QUOTES);
            return static::videoEmbed($arguments, "<video controls src='{$safeUrl}' controlsList='nodownload'></video>");
        }

        return parent::handle_shortcode($arguments, $content, $parser, $shortcode, $extra);
    }

    /**
     * Ensure flushing the cache clears videoEmbeds cache.
     */
    public static function flush(): void
    {
        Injector::inst()->get(CacheInterface::class . '.videoEmbeds')->clear();
    }

    /**
     * Allow S3 bucket domains to be excluded from sandboxing.
     *
     * IE. Do not wrap with iFrame.
     */
    protected static function updateDomainsExcludedFromSandboxing(): void
    {
        $cache = Injector::inst()->get(CacheInterface::class . '.videoEmbeds');
        $cachedDomains = $cache->get('videoDomains');
        if ($cachedDomains !== null) {
            $domains = $cachedDomains;
        } else {
            $s3buckets = S3Bucket::get();
            $domains = [];
            foreach ($s3buckets as $s3Bucket) {
                $domains[] = parse_url($s3Bucket->Domain, PHP_URL_HOST);
            }
            $ttl = static::config()->get('domains_ttl');
            $cache->set('videoDomains', $domains, $ttl);
        }
        Config::modify()->merge(EmbedShortcodeProvider::class, 'domains_excluded_from_sandboxing', $domains);
    }

    /**
     * Set the extractorUrl property.
     *
     * ReflectionClass is needed as $extractorUrl is a private property in the parent class.
     *
     * @param string $url The URL to set as the extractor URL.
     * This is the URL that will be checked for the sandboxing check.
     */
    protected static function setExtractorUrl(string $url): void
    {
        $reflection = new \ReflectionClass(get_parent_class(static::class));
        $property = $reflection->getProperty('extractorUrl');
        $property->setAccessible(true);
        $property->setValue(null, $url);
    }
}

<?php

namespace Signify\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Config;
use Signify\Views\Shortcodes\VideoFriendlyEmbedShortcodeProvider;
use Signify\Embeds\VideoFriendlyEmbedContainer;
use Psr\SimpleCache\CacheInterface;
use Signify\Models\S3Bucket;
use SilverStripe\View\Shortcodes\EmbedShortcodeProvider;

/**
 * Tests for the video friendly embed and shortcode functionality.
 */
class VideoFriendlyEmbedTests extends SapphireTest
{
    public function testDirectVideoIsDetected()
    {
        $directVideoUrl = 'https://signify.co.nz/video.mp4';
        $container = new VideoFriendlyEmbedContainer($directVideoUrl);
        $this->assertTrue($container->isDirectVideo());
    }

    public function testNonDirectVideoIsDetected()
    {
        $nonVideoUrl = 'https://signify.co.nz/video.mp3';
        $container = new VideoFriendlyEmbedContainer($nonVideoUrl);
        $this->assertFalse($container->isDirectVideo());
    }

    public function testHandleShortcodeReturnsVideoMarkupForDirectVideo()
    {
        $bucket = $this->createTestS3Bucket();

        // Check that video from sandbox excluded domain returns a <video>.
        $url = 'https://signify.co.nz/video.mp4';

        $shortcodeResult = VideoFriendlyEmbedShortcodeProvider::handle_shortcode(
            ['url' => $url],
            '',
            null,
            'embed'
        );

        $this->assertStringNotContainsString('<iframe', $shortcodeResult);
        $this->assertStringContainsString('<video', $shortcodeResult);
        $this->assertStringContainsString('controls', $shortcodeResult);
        $this->assertStringContainsString($url, $shortcodeResult);

        // Check that video from non-sandbox excluded domain returns an <iframe>.
        $url = 'https://signify.nz/video.mp4';
        $shortcodeResult = VideoFriendlyEmbedShortcodeProvider::handle_shortcode(
            ['url' => $url],
            '',
            null,
            'embed'
        );

        $this->assertStringContainsString('<iframe', $shortcodeResult);

        $bucket->delete();
    }

    public function testHandleShortcodeReturnsIFrameMarkupForNonDirectVideo()
    {
        $bucket = $this->createTestS3Bucket();

        // Check that video from non-sandbox excluded domain returns an <iframe>.
        $url = 'https://signify.nz/video.mp4';
        $shortcodeResult = VideoFriendlyEmbedShortcodeProvider::handle_shortcode(
            ['url' => $url],
            '',
            null,
            'embed'
        );

        $this->assertStringContainsString('<iframe', $shortcodeResult);

        $bucket->delete();
    }

    public function testUpdateDomainsExcludedFromSandboxing()
    {
        // Clear existing config to avoid conflicts
        Config::modify()->remove(EmbedShortcodeProvider::class, 'domains_excluded_from_sandboxing');

        // Clear the cache
        $cache = Injector::inst()->get(CacheInterface::class . '.videoEmbeds');
        $cache->delete('videoDomains');

        $bucket = $this->createTestS3Bucket();

        $method = new \ReflectionMethod(VideoFriendlyEmbedShortcodeProvider::class, 'updateDomainsExcludedFromSandboxing');
        $method->setAccessible(true);
        $method->invoke(null);  // null because it is static

        $domains = Config::inst()->get(EmbedShortcodeProvider::class, 'domains_excluded_from_sandboxing');
        $this->assertIsArray($domains);
        $this->assertContains('signify.co.nz', $domains);
        $this->assertNotEmpty($domains);

        $bucket->delete();
    }

    public function testGetHeightThrowsExceptionForInvalidAspectRatio()
    {
        VideoFriendlyEmbedContainer::config()->set('aspect_ratio_height', 5);
        VideoFriendlyEmbedContainer::config()->set('aspect_ratio_width', 0);

        $embed = new VideoFriendlyEmbedContainer('https://signify.co.nz/video.mp4');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid aspect ratio');

        $embed->getHeight();
    }

    public function testGetHeightCalculation()
    {
        VideoFriendlyEmbedContainer::config()->set('aspect_ratio_height', 9);
        VideoFriendlyEmbedContainer::config()->set('aspect_ratio_width', 16);
        VideoFriendlyEmbedContainer::config()->set('direct_video_width', 640);

        $embed = new VideoFriendlyEmbedContainer('https://signify.co.nz/video.mp4');

        $height = $embed->getHeight();
        $this->assertEquals(360, $height);
    }

    protected function createTestS3Bucket(): S3Bucket
    {
        $bucket = S3Bucket::create();
        $bucket->Domain = 'https://signify.co.nz';
        $bucket->write();

        return $bucket;
    }
}

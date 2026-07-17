<?php
/**
 * Updater unit tests (stubs).
 *
 * @package Google_Reviews_Plugin
 */

use PHPUnit\Framework\TestCase;

class GRP_Updater_Test extends TestCase {
    public function test_catalog_slug() {
        $this->assertSame('reactwoo-reviews', GRP_Updater::CATALOG_SLUG);
    }

    public function test_api_base_default() {
        $this->assertSame('https://api.reactwoo.com', GRP_Updater::get_api_base());
    }

    public function test_reactwoo_reviews_slug_constant() {
        $this->assertSame('reactwoo-reviews', REACTWOO_REVIEWS_SLUG);
    }
}

<?php
/** 
 * Admin page template
 *
 * @package WordPressTestAPI
 */

defined('ABSPATH') || exit;

use WordPressTestAPI\WordPress_Test_API;
?>

<div class="wrap">
    <h1><?php echo esc_html__('API Test Tools', WordPress_Test_API::SLUG); ?></h1>
    
    <?php if (!empty($data['cookie_string']) || !empty($data['nonce'])): ?>
        <div class="notice notice-info">
            <p><?php echo esc_html__('Copy these credentials to use with your API testing tools (e.g., Postman):', WordPress_Test_API::SLUG); ?></p>
        </div>

        <div class="api-example-image-container">
        <h3><?php echo esc_html__('Example of how to use the credentials in Postman'); ?></h3>
            <img src="https://img001.prntscr.com/file/img001/IbibgmOpSPKrfZtwPClQ3w.png"
                style="width: 100%; height: auto;" 
                alt="<?php echo esc_attr__('Postman API setup example', WordPress_Test_API::SLUG); ?>"
                class="api-example-image">
        </div>


        <div class="api-credentials">
            <h2><?php echo esc_html__('Cookie', WordPress_Test_API::SLUG); ?></h2>
            <div class="credential-field">
                <textarea readonly class="large-text code" rows="3"><?php echo esc_textarea($data['cookie_string']); ?></textarea>
                <button class="button copy-button" data-clipboard-target="cookie-string">
                    <?php echo esc_html__('Copy to Clipboard', WordPress_Test_API::SLUG); ?>
                </button>
            </div>

            <h2><?php echo esc_html__('X-WP-Nonce', WordPress_Test_API::SLUG); ?></h2>
            <div class="credential-field">
                <input type="text" readonly class="large-text code" value="<?php echo esc_attr($data['nonce']); ?>">
                <button class="button copy-button" data-clipboard-target="nonce">
                    <?php echo esc_html__('Copy to Clipboard', WordPress_Test_API::SLUG); ?>
                </button>
            </div>
        </div>

        <div class="usage-instructions">
            <h3><?php echo esc_html__('How to Use', WordPress_Test_API::SLUG); ?></h3>
            <ol>
                <li><?php echo esc_html__('Add the cookie string to your request headers under "Cookie"', WordPress_Test_API::SLUG); ?></li>
                <li><?php echo esc_html__('Add the X-WP-Nonce value to your request headers under "X-WP-Nonce"', WordPress_Test_API::SLUG); ?></li>
            </ol>
        </div>
    <?php else: ?>
        <div class="notice notice-error">
            <p><?php echo esc_html__('No session cookies or nonce found for the current user.', WordPress_Test_API::SLUG); ?></p>
        </div>
    <?php endif; ?>
</div> 
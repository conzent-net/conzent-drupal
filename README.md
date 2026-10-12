# Conzent CMP for Drupal

GDPR, CCPA & ePrivacy compliant cookie consent management for Drupal.

## Features

- **GDPR, CCPA & ePrivacy Compliance** -- Full regulatory compliance for cookie consent across jurisdictions.
- **IAB TCF v2.2 Certified** -- Transparency & Consent Framework support for programmatic advertising.
- **Google CMP Partner** -- Certified Google Consent Management Platform partner.
- **Google Consent Mode v2** -- Native integration with Google's consent signals for Analytics and Ads.
- **Automatic Cookie Blocking** -- Blocks cookies and scripts before user consent is given.
- **30+ Languages** -- Multi-language banner support out of the box.
- **Geo-Targeting** -- Show consent banners based on visitor location and applicable regulations.
- **Consent Logging** -- Full audit trail of user consent decisions for compliance documentation.
- **Self-Hosted Support** -- Works with both Conzent Cloud and self-hosted OCI installations.

## Requirements

- Drupal 10 or Drupal 11
- PHP 8.1 or higher

## Installation

### Via Composer (recommended)

```bash
composer require conzent/conzent-drupal
drush en conzent_drupal -y
```

### Manual Installation

1. Download and extract the module to `modules/custom/conzent_drupal/`
2. Enable the module at **Extend** (`/admin/modules`) or run:
   ```bash
   drush en conzent_drupal -y
   ```

## Configuration

1. Navigate to **Administration > Configuration > System > Conzent CMP**
   (`/admin/config/system/conzent`)
2. Enter your **Website Key** from your Conzent dashboard
3. Save the configuration -- the module will verify your website key automatically

The cookie consent banner will appear on all pages once a verified website key is configured.

## Self-Hosted (OCI) Support

If you are running a self-hosted Conzent OCI instance:

1. Enter your OCI server URL in the **Server URL** field (e.g. `https://consent.yourdomain.com`)
2. The module will load the banner script from your server instead of Conzent Cloud
3. Verification will also use your self-hosted API endpoint

Leave the Server URL empty to use Conzent Cloud (default: `https://app.getconzent.com`).

## Links

- **Website**: https://getconzent.com
- **Dashboard**: https://app.getconzent.com
- **Documentation**: https://getconzent.com/docs
- **GitHub**: https://github.com/conzent-net/oci

## License

This project is licensed under the GNU General Public License v3.0 or later.
See the [LICENSE.txt](LICENSE.txt) file for details.

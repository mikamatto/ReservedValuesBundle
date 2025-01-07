# ReservedValuesBundle

**ReservedValuesBundle** is a Symfony bundle that allows you to restrict the use of reserved values across different fields by adding custom validation rules. You can specify exact restrictions or use patterns to block specific values, applicable to any field, such as usernames, slugs, or any other user-defined attributes that require validation.

## Features

- Block specific exact usernames (e.g., `admin`, `support`).
- Block usernames based on patterns (e.g., usernames starting with `admin` or `support`).
- Support for multiple keys to apply a different set of restrictions to different fields.
- Automatic validation bypass for admin users (hierarchically) based on a configurable role.

## Installation

### 1. Require the Bundle

You can install the bundle using Composer:

```bash
composer require mikamatto/reserved-values-bundle
```

### 2. Enable the Bundle

If you're using Symfony Flex, the bundle will be enabled automatically. Otherwise, you may need to manually enable it in your `config/bundles.php`:

```php
return [
    // Other bundles...
    Mikamatto\ReservedValuesBundle\ReservedValuesBundle::class => ['all' => true],
];
```

### 3. Configuration

You need to create a configuration file for the bundle at `config/packages/reserved_values.yaml`.

The format of the configuration file is as follows:

```yaml
reserved_values:
    # Optional: Configure the minimum role required to bypass validation
    bypass_role: ROLE_ADMIN  # Default value if not specified

    username:
        exact:
          - _error
          - _wdt
          - _profiler
          - account
          - configuration
          - contact
          - posts
          - dmca
          - login
          - logout
          - page
          - password
          - register
          - scheduler
          - settings
          - slugger
          - sfw
          - user

        patterns:
          - '^admin.*'    # Bans 'admin', 'administrator', 'admin123', etc.
          - '^support.*'  # Bans 'support', 'support1', 'support-team', etc.

    slug:
        exact:
          - example-slug
          - another-slug

        patterns:
          - '^draft-.*'   # Bans 'draft-post', 'draft-article', etc.
```

#### Configuration Options

- **key**: This is a custom identifier for the field (e.g., username, slug) to which the restrictions apply.
- **exact**: This section contains a list of values that are strictly forbidden for the specified key. Users attempting to use any of these values will receive a validation error.
- **patterns**: This section allows you to specify regular expressions for matching values that should be restricted. Any value that matches one of the defined patterns will also trigger a validation error.
- **bypass_role**: This is an optional configuration that specifies the minimum role required to bypass the validation. If not specified, the default value is `ROLE_ADMIN`.

Make sure to define at least one of the options under each key in your configuration file. If both sections are left empty for a key, no values will be restricted for that field.

### 4. Applying the Validation to an Entity

To apply the `ReservedValues` validation to your entity (for example, User), you need to include the constraint in your entity class. You can do this either with annotations or attributes. Here’s how to do it with both methods:

#### Using Annotations
```php
namespace App\Entity;

use Mikamatto\ReservedValuesBundle\Validator\Constraints\ReservedValues;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\NotBlank
     * @ReservedValues(key="username")
     */
    private $username;

    // Other properties and methods...
}
```
#### Using Attributes (Symfony 6.0+)

If you prefer using PHP attributes instead of annotations, you can do so like this:
```php
namespace App\Entity;

use Mikamatto\ReservedValuesBundle\Validator\Constraints\ReservedValues;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    #[Assert\NotBlank]
    #[ReservedValues(key: "username")]
    private string $username;

    // Other properties and methods...
}
```

In the examples above, both methods trigger validation whenever a User instance is validated, ensuring that the specified usernames are restricted according to your configuration.

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
    # Optional: Configure the roles that can bypass validation
    bypass_roles:
        - ROLE_ADMIN # The default value if not specified
        - ROLE_SYS_ADMIN
        - ROLE_DATA_ADMIN

    keys:
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
- **bypass_roles**: This is an optional configuration that specifies the roles required to bypass the validation. If not specified, the default value is `ROLE_ADMIN`.

Make sure to define at least one of the options under each key in your configuration file. If both sections are left empty for a key, no values will be restricted for that field.

### 4. Applying the Validation to an Entity

To apply the `ReservedValues` validation to your entity, you can use either annotations or attributes. The constraint accepts:
- `key`: (required) The key for the validation rules defined in your configuration
- `bypassRoles`: (optional) Additional roles that can bypass validation for this specific field. Can be a single role as string or an array of roles. Defaults to an empty array, meaning only global bypass roles from configuration will apply.

Here are examples using both methods:

#### Using Annotations
```php
namespace App\Entity;

use Mikamatto\ReservedValuesBundle\Validator\Constraints\ReservedValues;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\NotBlank
     * @ReservedValues(key="username")  // Only global bypass roles apply
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @ReservedValues(key="username", bypassRoles={"ROLE_USER_ADMIN"})  // Global + field-specific roles
     */
    private $anotherField;
}
```

#### Using Attributes (Symfony 6.0+)
```php
namespace App\Entity;

use Mikamatto\ReservedValuesBundle\Validator\Constraints\ReservedValues;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    #[Assert\NotBlank]
    #[ReservedValues('username')]  // Only global bypass roles apply
    private $username;

    #[Assert\NotBlank]
    #[ReservedValues('username', ['ROLE_USER_ADMIN', 'ROLE_CUSTOM_ADMIN'])]  // Multiple field-specific roles
    private $secondField;

    #[Assert\NotBlank]
    #[ReservedValues('username', 'ROLE_USER_ADMIN')]  // Single field-specific role
    private $thirdField;
}
```

The validation will be bypassed if the user has either:
- Any of the global roles defined in the configuration under `bypass_roles`
- Any of the field-specific roles defined in the attribute (if specified)

This allows for both global and field-specific role-based validation bypass while maintaining backward compatibility with existing code that only uses the `key` parameter.

## Upgrading from 1.x to 2.0

Version 2.0 introduces a new configuration structure and the ability to configure multiple bypass roles. Here's how to upgrade your existing configuration:

### Configuration Changes

#### Before (1.x)
```yaml
reserved_values:
    username:
        exact:
            - admin
            - support
        patterns:
            - '^admin.*'
```

#### After (2.0)
```yaml
reserved_values:
    # Optional: Configure roles that can bypass validation
    bypass_roles:
        - ROLE_ADMIN # The default value if not specified
        - ROLE_SYS_ADMIN
        - ROLE_DATA_ADMIN

    keys:
        username:
            exact:
                - admin
                - support
            patterns:
                - '^admin.*'
```

### Breaking Changes
1. All validation rules must now be placed under the `keys` section
2. The role-based validation bypass is now configurable through `bypass_roles`
3. The configuration structure has changed to better separate concerns

### Migration Steps
1. Add the new `keys` root node to your configuration
2. Move all your existing validation rules under the `keys` section
3. If you want to customize which roles can bypass validation, add the `bypass_roles` configuration
4. If you don't specify `bypass_roles`, it defaults to `['ROLE_ADMIN']`

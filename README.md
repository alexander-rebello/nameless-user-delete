# NamelessMC User Delete Module

A comprehensive user account deletion module for NamelessMC that allows users to safely delete their accounts while preserving forum contributions through anonymization.

## 📋 Overview

The User Delete module provides a secure way for users to delete their accounts from your NamelessMC forum. Instead of completely removing all user data (which would break forum threads and discussions), the module anonymizes personal information while preserving posts and contributions under an anonymized name.

## ✨ Features

- **Safe Account Deletion**: Anonymizes user data while preserving forum contributions
- **Security Measures**: Requires password confirmation and username verification
- **Admin Protection**: Prevents administrators from accidentally deleting their accounts
- **Multi-language Support**: Includes English (US/UK) and German language files
- **Permission-based Access**: Uses NamelessMC's permission system
- **User-friendly Interface**: Clean, responsive template design
- **Audit Trail**: Maintains forum integrity by preserving posts

## 🛡️ Security Features

- **Password Verification**: Users must enter their current password
- **Username Confirmation**: Double verification by typing username
- **Admin Safeguards**: Administrators cannot delete their accounts
- **Permission Checks**: Only users with proper permissions can access deletion
- **CSRF Protection**: Token-based form protection

## 📦 Installation

1. **Download the module** files to your NamelessMC installation
2. **Upload the files** to your NamelessMC directory:
   ```
   /uploads/modules/UserDelete/
   /uploads/custom/templates/DefaultRevamp/user/
   ```
3. **Navigate** to your NamelessMC Admin Panel
4. **Go to** Modules section
5. **Enable** the User Delete module
6. **Configure permissions** as needed

## 📁 File Structure

```
uploads/
├── modules/
│   └── UserDelete/
│       ├── init.php              # Module initialization
│       ├── module.php            # Main module class
│       ├── language/             # Language files
│       │   ├── en_US.json       # English (US)
│       │   ├── en_UK.json       # English (UK)
│       │   └── de_DE.json       # German
│       └── pages/
│           └── user/
│               └── delete.php    # Account deletion page
└── custom/
    └── templates/
        └── DefaultRevamp/
            └── user/
                └── delete_account.tpl  # Template file
```

## 🔧 Configuration

### Permissions

The module registers the following permission:

- `delete_user.delete` - Profile Settings » Delete own account

### User Access

Once installed, users can access the account deletion feature through:

- **User Control Panel** → **Delete Account**
- **Direct URL**: `/user/delete`

## 🌐 Language Support

The module includes comprehensive language support:

- **English (US)** - `en_US.json`
- **English (UK)** - `en_UK.json`
- **German** - `de_DE.json`

### Adding New Languages

To add support for additional languages:

1. Copy an existing language file from `/uploads/modules/UserDelete/language/`
2. Rename it to your locale (e.g., `fr_FR.json` for French)
3. Translate all text strings in the JSON file
4. The module will automatically detect and use the new language

## 🎨 Template Customization

The module includes a template file for the DefaultRevamp theme:

- `delete_account.tpl` - Account deletion form template

To customize for other themes:

1. Copy the template to your theme's directory
2. Modify the styling and structure as needed
3. Ensure all Smarty variables are maintained

## 💻 Technical Details

- **Version**: 2.3.4
- **Author**: Alexander Rebello
- **License**: MIT
- **NamelessMC Compatibility**: 2.2.3+
- **PHP Requirements**: Compatible with NamelessMC requirements

### What Happens During Account Deletion

1. **Verification**: Password and username confirmation
2. **Permission Check**: Ensures user has deletion rights
3. **Admin Check**: Prevents admin account deletion
4. **Data Anonymization**:
   - Username changed to anonymized format
   - Email address removed
   - Profile information cleared
   - Personal data anonymized
5. **Content Preservation**: Posts and forum contributions remain visible

## 🤝 Contributing

Contributions are welcome! Please feel free to:

- Report bugs
- Suggest new features
- Submit pull requests
- Improve translations

## 📄 License

This project is licensed under the MIT License - see the source files for details.

## 👨‍💻 Author

**Alexander Rebello**

- Website: [alexander-rebello.de](https://www.alexander-rebello.de)

## 🔗 Links

- [NamelessMC Official Website](https://namelessmc.com/)
- [NamelessMC Documentation](https://docs.namelessmc.com/)

## ⚠️ Important Notes

- **Irreversible Action**: Account deletion cannot be undone
- **Data Preservation**: Posts and contributions are preserved for forum integrity
- **Admin Safety**: Administrators cannot delete their accounts through this module
- **Backup Recommended**: Always backup your database before installing new modules

---

_This module helps maintain GDPR compliance by providing users with account deletion capabilities while preserving the integrity of forum discussions._

# Razoyo Car Profile Module - README

## Overview

The Razoyo Car Profile module for Magento 2 allows customers to select and view detailed information about their cars directly from their account dashboard. This module enhances the customer experience by providing a seamless way to manage car profiles with real-time updates and smooth interactions.

## Features

- **Enable/Disable Module**: The module can be easily enabled or disabled through the Magento admin panel. By default, the module is enabled to save time during setup.
- **AJAX Integration**: Utilizes AJAX to save and display car details without requiring a page reload. This enhances the user experience by providing faster and more responsive interactions.
- **User-Friendly Notifications**: Shows appropriate success or failure messages when car details are saved or updated. Ensures users are informed about the status of their actions without displaying raw errors.
- **Luma Theme Compatibility**: Designed to work seamlessly with the Luma theme, providing a consistent and visually appealing user interface.

## Installation

1. Copy the module files to the Magento 2 `app/code/Razoyo/CarProfile` directory.
2. Run the following commands in the Magento root directory:
   ```bash
   bin/magento module:enable Razoyo_CarProfile
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento setup:static-content:deploy
   ```

## Made By

Name: Sagar Parikh
Email: sagar.parikh69@gmail.com
Phone: +91-7359944562




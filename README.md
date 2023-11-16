# UM Number of Days ago
Extension to Ultimate Member for display of dates in the Members Directory either as WP human time difference or only as number of days difference. A Shortcode for these time differences in the User Profile page.

## A. Members Directory
## UM Settings -> Misc
1. Number of Days ago - Meta Keys WP human - Select the date meta_key fields to use for the WP human time difference in Members Directory.
2. Number of Days ago - Meta Keys Days ago - Select the date meta_key fields to use for "x days ago" in Members Directory.

## B. Shortcode "number_of_days_ago"
1. [number_of_days_ago meta_key="birth_date" type="WP"] old 
2. [number_of_days_ago meta_key="birth_date" type="days"]

## Display Formats "days"
1. one day ago / %d days ago
2. one hour ago / %d hours ago
3. one minute ago / %d minutes ago
4. less than one minute ago

## Display Formats "WP"
1. https://developer.wordpress.org/reference/functions/human_time_diff/
2. For meta_key <code>birth_date</code> addition of "old" and other meta_keys addition of "ago"when used in Members Directory.
3. For Shortcode usage add your "old" or "ago" before/after the Shortcode as in the example above.
4. WP is doing a round up of the values which will result in 1.7 being 2. This round up is not used for <code>birth_date</code> by this plugin.

## Updates
1. Version 1.1.0 Excluded birth_date from WP human_time_diff and made the calculation of years in the plugin because of rounding up the value by WP.
2. Version 1.2.0 Updated the WP years old calculation.

## Installation
1. Install by downloading the plugin ZIP file and install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
2. Activate the Plugin: Ultimate Member - Number of Days ago


